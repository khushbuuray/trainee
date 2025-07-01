<?php declare(strict_types=1);

namespace zenit\PlatformGravity\Administration\Controller;

use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route(defaults: ['_routeScope' => ['api']])]
#[Package('administration')]

class ThemeConfigPresetController
{
    private EntityRepository $mediaRepository;
    private EntityRepository $mediaFolderRepository;
    private FileSaver $fileSaver;

    private String $projectDir;

    /**
     * ThemeConfigPreset constructor.
     *
     * @param EntityRepository $mediaRepository
     * @param EntityRepository $mediaFolderRepository
     * @param FileSaver $fileSaver
     * @param String $projectDir
     *
     */
    public function __construct(EntityRepository $mediaRepository, EntityRepository $mediaFolderRepository, FileSaver $fileSaver, String $projectDir)
    {
        $this->mediaRepository = $mediaRepository;
        $this->mediaFolderRepository = $mediaFolderRepository;
        $this->fileSaver = $fileSaver;
        $this->projectDir = $projectDir;
    }

    /**
     * Method to import the theme media
     *
     * @param Request $request
     * @return JsonResponse
     *
     */
    #[Route(path: '/api/zen/theme/gravity/config-preset-processor', name: 'api.zen.theme.gravity.config-preset-processor', methods: ['POST'])]
    public function themeConfigProcessor (Request $request): JsonResponse
    {
        $configFields = json_decode($request->getContent());

        $mediaFieldNames = $mediaPaths = $mediaIds = [];

        foreach ($configFields as $key => $configField) {
            if (property_exists($configField, 'type') && $configField->type == 'media') {
                if (property_exists($configField, 'value') && $configField->value != '') {
                    $mediaFieldNames[] = $key;
                    $mediaPaths[] = $configField->value;
                }
            }
        }

        foreach ($mediaPaths as $mediaPath) {
            if (str_contains($mediaPath, '.')) {
                $mediaIds[] = $this->importThemeMedia($mediaPath);
            }
        }

        if ($mediaIds && count($mediaIds) != 0) {
            foreach ($mediaFieldNames as $key => $mediaFieldName) {
                $configFields->{$mediaFieldName}->value = $mediaIds[$key];
            }
        }

        return new JsonResponse([$configFields]);
    }

    /**
     * Method to import the theme media
     *
     * @param String $mediaPath
     * @return String
     *
     */
    private function importThemeMedia (String $mediaPath): String {
        $context = Context::createDefaultContext();
        $themeMediaFolderId = $this->getThemeMediaDefaultFolderId($context);
        $existingMediaId = $this->imageExists($context, pathinfo(basename($mediaPath), PATHINFO_FILENAME), $themeMediaFolderId);

        $mediaPath = str_replace('app/storefront/dist/assets/', '', $mediaPath);

        if ($existingMediaId == '') {
            $mediaId = Uuid::randomHex();

            $this->mediaRepository->create([
                [
                    'id' => $mediaId,
                    'mediaFolderId' => $themeMediaFolderId,
                ]
            ], $context);

            $file = \glob($this->projectDir . '/public/bundles/zenitplatformgravity/storefront/assets/configimport/' . $mediaPath)[0];
            if ($file === false) {
                return '';
            }

            $this->fileSaver->persistFileToMedia(
                new MediaFile(
                    $file,
                    \mime_content_type($file) ?: 'application/octet-stream',
                    \pathinfo($file, PATHINFO_EXTENSION),
                    \filesize($file) ?: 0
                ),
                \pathinfo($file, PATHINFO_FILENAME),
                $mediaId,
                $context
            );

            return $mediaId;
        }

        return $existingMediaId;
    }

    /**
     * Method to check if image is existing.
     *
     * @param Context $context
     * @param String $mediaName
     * @param String $mediaFolderId
     * @return String
     *
     */
    private function imageExists(Context $context, String $mediaName, String $mediaFolderId): String {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mediaFolderId', $mediaFolderId));

        $result = $this->mediaRepository->search($criteria, $context);

        if ($result->getTotal() !== 0) {
            foreach ($result->getEntities()->getElements() as $element) {
                if ($element->get('fileName') == $mediaName) {
                    return $element->get('id');
                }
            }
        }

        return '';
    }

    private function getThemeMediaDefaultFolderId(Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('media_folder.defaultFolder.entity', 'theme'));
        $criteria->setLimit(1);

        $mediaFolder = $this->mediaFolderRepository->search($criteria, $context)->getEntities();

        if ($mediaFolder->count() !== 1 || $mediaFolder->first() === null) {
            throw new \RuntimeException('Default Theme folder does not exist.');
        }

        return $mediaFolder->first()->getId();
    }
}