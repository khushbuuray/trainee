
import template from "./sw-cms-el-buy-button.html.twig";
import "./sw-cms-el-buy-button.scss";

const { Mixin } = Shopware;

Shopware.Component.register("sw-cms-el-buy-button", {
    template,

    compatConfig: Shopware.compatConfig,

    mixins: [Mixin.getByName("cms-element")],

  
created(){
        this.createdComponent();
},
    methods: {

        createdComponent() {
            this.initElementConfig('custom-text-image');
            this.initElementData("button");
        },
    },
});
