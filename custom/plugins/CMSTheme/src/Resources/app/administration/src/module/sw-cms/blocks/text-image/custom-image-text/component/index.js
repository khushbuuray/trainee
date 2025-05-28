import template from "./sw-cms-block-custom-image-text.html.twig";
import "./sw-cms-block-custom-image-text.scss";

Shopware.Component.register("sw-cms-block-custom-image-text", {
    template,
    compactConfig: Shopware.compatConfig
});