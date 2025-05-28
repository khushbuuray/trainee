import template from "./sw-cms-el-config-buy-button.html.twig";
import "./sw-cms-el-config-buy-button.scss";
const { Mixin } = Shopware;
Shopware.Component.register("sw-cms-el-config-buy-button", {
    template,
    compatConfig: Shopware.compatConfig,

    emits: ["element-update"],

    mixins: [Mixin.getByName("cms-element")],

    created() {
        this.createdComponent();
    },

    methods: {
createdComponent(){
            this.initElementConfig("buy-button");
},
        onChange() {
            this.$emit("element-update", this.element);
        },
    },

});