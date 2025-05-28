
import './preview';
import './component';
import './config';

/**
 * @private
 * @sw-package discovery
 */
Shopware.Service("cmsService").registerCmsElement({
    name:"custom-button",
    label:"sw-cms.elements.buyButton.label",
    component:"sw-cms-el-buy-button",
    configComponent:"sw-cms-el-config-buy-button",
    previewComponent:"sw-cms-el-preview-buy-button",
    disabledConfigInfoTextKey:
        "sw-cms.elements.buyButton.infoText.tooltipSettingDisabled",
    defaultConfig:{
        name:{
            source:"static",
            value:"shop",
            required:true,
        },
        link:{
            source:"static",
            value:null,
        }
    },
    collect:Shopware.Service("cmsService").getCollectFunction(),
});
