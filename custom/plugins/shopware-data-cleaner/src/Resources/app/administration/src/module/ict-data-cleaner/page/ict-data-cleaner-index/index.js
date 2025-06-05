// import template from "./ict-data-cleaner-index.html.twig"
// import "./ict-data-cleaner-index.scss"

// const { Component, Mixin } = Shopware
// const { Criteria } = Shopware.Data
  
// Component.register("ict-data-cleaner-index", {
//   template,

//   inject: ["repositoryFactory", "systemConfigApiService", "httpClient"],

//   mixins: [Mixin.getByName("notification")],

//   data() {
//     return {
//       isLoading: false,
//       isSaveSuccessful: false,
//       isDryRun: true,
//       previewResults: null,
//       showPreview: false,
//       showConfirmModal: false,
//       totalItemsToDelete: 0,
//       salesChannelId: null,
//       config: null,
//       productSettings: {}, // so it'
//     }
//   },

//   metaInfo() {
//     return {
//       title: this.$tc("ict-data-cleaner.general.title"),
//     }
//   },

//   computed: {
//     configDomain() {
//       return "IctDataCleanerPro.config"
//     },
//   },

//   created() {    
//     this.createdComponent()
//   },

//   methods: {
//     createdComponent() {

//       this.isLoading = true
//       this.systemConfigApiService
//         .getValues(this.configDomain)
//         .then((response) => {
//           this.config = response
//           this.isDryRun = this.config.dryRunMode || true
//         })
//         .finally(() => {
//           this.isLoading = false
//         })
//     },

//     onSaveConfig() {
//       this.isLoading = true
//       this.isSaveSuccessful = false

//       this.systemConfigApiService
//         .saveValues(this.config)
//         .then(() => {
//           this.isSaveSuccessful = true
//         })
//         .catch((error) => {
//           this.createNotificationError({
//             title: this.$tc("global.default.error"),
//             message: error.message,
//           })
//         })
//         .finally(() => {
//           this.isLoading = false
//         })
//     },

//     onPreviewCleanup() {
//       this.isLoading = true
//       this.showPreview = false
//       this.previewResults = null

//       this.httpClient
//         .post(
//           `${Shopware.Context.api.apiPath}/ict-data-cleaner/preview`,
//           {},
//           { headers: this.httpClient.getBasicHeaders() },
//         )
//         .then((response) => {
//           const data = response.data.data
//           this.previewResults = data
//           this.showPreview = true
//           this.calculateTotalItems()
//         })
//         .catch((error) => {
//           this.createNotificationError({
//             title: this.$tc("global.default.error"),
//             message: error.message,
//           })
//         })
//         .finally(() => {
//           this.isLoading = false
//         })
//     },

//     onRunCleanup() {
//       if (this.isDryRun) {
//         this.showConfirmModal = true
//         return
//       }

//       this.executeCleanup()
//     },

//     onConfirmCleanup() {
//       this.showConfirmModal = false
//       this.executeCleanup()
//     },

//     executeCleanup() {
//       this.isLoading = true

//       this.httpClient
//         .post(
//           `${Shopware.Context.api.apiPath}/ict-data-cleaner/cleanup`,
//           { dryRun: this.isDryRun },
//           { headers: this.httpClient.getBasicHeaders() },
//         )
//         .then((response) => {
//           const data = response.data.data
//           this.createNotificationSuccess({
//             title: this.$tc("ict-data-cleaner.general.successTitle"),
//             message: this.$tc("ict-data-cleaner.general.successMessage", 0, {
//               count: this.calculateTotalItemsFromResults(data),
//             }),
//           })
//         })
//         .catch((error) => {
//           this.createNotificationError({
//             title: this.$tc("ict-data-cleaner.general.errorTitle"),
//             message: error.message || this.$tc("ict-data-cleaner.general.errorMessage"),
//           })
//         })
//         .finally(() => {
//           this.isLoading = false
//         })
//     },

//     calculateTotalItems() {
//       this.totalItemsToDelete = this.calculateTotalItemsFromResults(this.previewResults)
//     },

//     calculateTotalItemsFromResults(results) {
//       let total = 0
//       if (!results) {
//         return total
//       }

//       Object.values(results).forEach((handlerResult) => {
//         if (handlerResult.items) {
//           Object.values(handlerResult.items).forEach((item) => {
//             total += item.count || 0
//           })
//         }
//       })

//       return total
//     },
//   },
// })



import template from './ict-data-cleaner-index.html.twig';
import "./ict-data-cleaner-index.scss"

const { Component } = Shopware;

Component.register('ict-data-cleaner-index', {
    template,
     props: {
        config: {
            type: Object,
            required: true
        }
    },

  // inject: ["repositoryFactory", "systemConfigApiService", "httpClient"],
  inject: [
  "repositoryFactory", 
  "systemConfigApiService", 
  "httpClient",
  "ictDataCleanerService"
],

created() {
    const httpClient = this.httpClient;

    this.ictDataCleanerService = {
        previewCleanup(config) {
            return httpClient.post('/api/_action/ict-data-cleaner/preview-cleanup', config);
        }
    };

    this.loadSystemConfig();
},


    data() {
    return {
        productSettings: {},
        isLoading: true,
        activeTab: 'products',
        showPreviewModal: false,
        // isLoading: false,
      isSaveSuccessful: false,
      isDryRun: true,
      previewResults: null,
      showConfirmModal: false,
      totalItemsToDelete: 0,
      salesChannelId: null,
      config: null,
    };
},

methods: {
    async loadSystemConfig() {
    this.isLoading = true;

    this.systemConfigApiService.getValues(this.configDomain, this.salesChannelId)
        .then((config) => {
            this.productSettings = config;
        })
        .finally(() => {
            this.isLoading = false; // <--- Important!
        });
},

    async previewAction() {
         this.productSettings = await this.loadProductSettings(); // Make sure this works
        this.showPreviewModal = true;
    },

    handleModalClose() {
        this.showPreviewModal = false;
    },

    handleModalConfirm() {
        this.showPreviewModal = false;
        console.log('Running cleanup with config:', this.productSettings);
        // Call your API here if needed
    },
      onPreviewCleanup() {
  this.isLoading = true;

    this.ictDataCleanerService.previewCleanup(this.productSettings).then((result) => {
        this.previewResult = result;
        this.showPreviewModal = true;
    }).catch((error) => {
        console.error('Preview failed:', error);
        this.createNotificationError({
            title: 'Preview Error',
            message: 'Could not load preview. Check console for details.'
        });
    }).finally(() => {
        this.isLoading = false;
    });
    },
}

});
