// import template from "./ict-data-cleaner-index.html.twig";
// import "./ict-data-cleaner-index.scss";

// const { Component, Mixin } = Shopware;
// const { Criteria } = Shopware.Data;

// Component.register("ict-data-cleaner-index", {
//   template,

//   inject: [
//     "repositoryFactory",
//     "systemConfigApiService",
//     "ictDataCleanerService",
//   ],

//   mixins: [Mixin.getByName("notification")],

//   data() {
//     return {
//       isLoading: false,
//       activeTab: "products",
//       isSaveSuccessful: false,
//       isDryRun: true,
//       previewResults: null,
//       showPreviewModal: false,
//       showConfirmModal: false,
//       totalItemsToDelete: 0,
//       salesChannelId: null,
//       config: null,
//       previewData: null,
//       selectedPreviewProducts: {},
//       gridKey: 0,
//       gridReady: false,
//       pagination: {},
//       productSettings: {
//         "IctDataCleaner.config.productCleanup.monthsNotSold": 6,
//         "IctDataCleaner.config.productCleanup.deleteNeverSold": false,
//         "IctDataCleaner.config.productCleanup.monthsDisabled": 6,
//         "IctDataCleaner.config.productVariantCleanup.zeroStockMonths": 6,
//       },
//       customerSettings: {
//         "IctDataCleaner.config.customerCleanup.guestMonths": 12,
//         "IctDataCleaner.config.customerCleanup.inactiveMonths": 12,
//       },
//       cartSettings: {
//         "IctDataCleaner.config.cartCleanup.abandonedDays": 30,
//         "IctDataCleaner.config.orderCleanup.cancelledAgeMonths": 12,
//         "IctDataCleaner.config.orderCleanup.oldAgeMonths": 12,
//       },
//       categorySettings: {
//         "IctDataCleaner.config.categoryCleanup.emptyCategories": true,
//         "IctDataCleaner.config.categoryCleanup.noSalesMonths": 6,
//       },
//       promotionSettings: {
//         'IctDataCleaner.config.promotionCleanup.expiredMonths': 6,
//         'IctDataCleaner.config.promotionCleanup.unusedVoucherMonths': 6,
//         'IctDataCleaner.config.promotionCleanup.orphaned': true,
//       },
//       reviewSettings: {
//         "IctDataCleaner.config.reviewCleanup.unapprovedDays": 30,
//       },
//       cmsPageSettings: {
//         "IctDataCleaner.config.cmsPageCleanup.neverViewedMonths": true,
//         "IctDataCleaner.config.cmsPageCleanup.unpublishedDraftsMonths": 6,
//       },
//       newsletterSettings: {
//         "IctDataCleaner.config.newsletterCleanup.bouncedMonths": 6,
//       },
//       mediaSettings: {
//         "IctDataCleaner.config.mediaCleanup.orphanAgeDays": 60,
//         "IctDataCleaner.config.mediaCleanup.deleteThumbnails": true,
//       },
//       systemLogSettings: {
//         "IctDataCleaner.config.systemLogCleanup.months": 6,
//         // "IctDataCleaner.config.systemLogCleanup.cacheClear": true,
//         // "IctDataCleaner.config.systemLogCleanup.orphaned": true,
//       },

//     };
//   },

//   computed: {
//     configDomain() {
//       return "IctDataCleanerPro.config";
//     },

//     paginatedRows() {
//       const result = {};
//       if (!this.previewData?.items) return result;

//       for (const [key, group] of Object.entries(this.previewData.items)) {
//         const page = this.pagination?.[key]?.page || 1;
//         const limit = this.pagination?.[key]?.limit || 10;
//         result[key] = (group.sample || []).slice(
//           (page - 1) * limit,
//           page * limit
//         );
//       }

//       return result;
//     },
//   },

//   methods: {
//     getColumnsForGroup(key) {
//       const sample = this.previewData?.items?.[key]?.sample;
//       console.log("Preview sample for", key, sample);

//       if (!Array.isArray(sample) || sample.length === 0) return [];

//       const firstRow = sample[0];
//       console.log("First row:", firstRow);

//       if (typeof firstRow !== "object" || firstRow === null) {
//         console.warn(`Invalid row data for preview group: ${key}`, firstRow);
//         return [];
//       }

//       // return Object.keys(firstRow).map((field) => ({
//       //   property: field,
//       //   label: this.beautifyLabel(field),
//       // }));
//       return Object.keys(firstRow)
//   .filter(field => field !== 'id')
//   .map(field => ({
//     property: field,
//     label: this.beautifyLabel(field),
//   }));
//     },
//     beautifyLabel(field) {
//       console.log("Beautify label:", field);  
//   //       if (field === 'id') {
//   //   return null; // or return ''; or return undefined;
//   // }
      
//       return field
//         .replace(/_/g, " ")
//         .replace(/\b\w/g, (char) => char.toUpperCase());
//     },
//     setActiveTab(tabKey) {
//       this.activeTab = tabKey;
//       console.log("Switched tab to:", tabKey);
//     },
//     previewAction(field) {
//       console.log("Preview action field:", field);

//       this.gridReady = false;
//       this.activePreviewKey = field;

//       const httpClient = Shopware.Application.getContainer("init").httpClient;
//       const loginService = Shopware.Service("loginService");
//       const token = loginService.getToken();

//       if (!token) {
//         console.error("Auth token not found.");
//         return;
//       }

//       // const config = {
//       //   [field]: this.productSettings[`IctDataCleaner.config.${field}`],
//       // };
//       const key = `IctDataCleaner.config.${field}`;
//       const value = this.allSettings()[key];

//       if (value === undefined) {
//         console.warn(` No setting found for: ${key}`);
//         return;
//       }
//       const config = { [key]: value };

//       console.log("Preview config:", config);
//       httpClient
//         .post("/ict-data-cleaner/preview", config, {
//           headers: { Authorization: `Bearer ${token}` },
//         })
//         .then((response) => {
//           this.previewData = response.data.data;
//           this.selectedPreviewProducts = {};
//           this.pagination = {};

//           Object.entries(this.previewData.items).forEach(
//             ([groupKey, group]) => {
//               const ids = (group.sample || []).map((p) => p.id);
//               this.$set(this.selectedPreviewProducts, groupKey, ids);
//               this.$set(this.pagination, groupKey, {
//                 page: 1,
//                 limit: 10,
//                 total: group.count || 0,
//               });
//             }
//           );

//           this.gridKey += 1;

//           this.$nextTick(() => {
//             this.gridReady = true;
//             this.showPreviewModal = true;

//             this.$nextTick(() => {
//               requestAnimationFrame(() => {
//                 const previewGrids = this.$refs.previewGrid || {};

//                 if (Array.isArray(previewGrids)) {
//                   previewGrids.forEach((gridRef, index) => {
//                     if (gridRef?.selectAll) {
//                       gridRef.selectAll(true);
//                       console.log(`✅ Auto-selected all rows in grid ${index}`);
//                     }
//                   });
//                 } else {
//                   Object.keys(this.previewData.items).forEach((key) => {
//                     const gridRef = previewGrids[key];
//                     if (gridRef?.selectAll) {
//                       gridRef.selectAll(true);
//                       console.log(`✅ Auto-selected all rows in grid: ${key}`);
//                     } else {
//                       console.warn(
//                         `⚠️ Grid ref not found for: ${key}`,
//                         previewGrids
//                       );
//                     }
//                   });
//                 }
//               });
//             });
//           });
//         })
//         .catch((error) => {
//           console.error(
//             "Preview request failed:",
//             error.response?.data?.errors ?? error.message
//           );
//         });
//     },

//     onProductSelectionChange(groupKey, selectedIds) {
//       this.$set(this.selectedPreviewProducts, groupKey, selectedIds);
//       console.log(`Selected IDs for ${groupKey}:`, selectedIds);
//     },

//     onPageChange({ page, limit }, groupKey) {
//       // Update pagination state
//       if (!this.pagination) this.pagination = {};
//       this.$set(this.pagination, groupKey, {
//         page,
//         limit,
//         total: this.previewData?.items?.[groupKey]?.sample?.length || 0,
//       });

//       this.gridReady = false;

//       this.$nextTick(() => {
//         this.gridKey += 1;

//         this.$nextTick(() => {
//           this.gridReady = true;

//           this.$nextTick(() => {
//             // Make sure this group exists
//             const group = this.previewData?.items?.[groupKey];
//             if (!group) return;

//             // Slice current page sample
//             const currentPageItems = (group.sample || [])
//               .slice((page - 1) * limit, page * limit)
//               .map((p) => p.id);

//             // Existing selection for this group
//             const currentSelected = Array.isArray(
//               this.selectedPreviewProducts[groupKey]
//             )
//               ? this.selectedPreviewProducts[groupKey]
//               : [];

//             // Merge old selection + new page items (prevent duplicates)
//             const updatedSelection = [
//               ...new Set([...currentSelected, ...currentPageItems]),
//             ];

//             this.$set(this.selectedPreviewProducts, groupKey, updatedSelection);

//             // Select all visible rows in the current page
//             const gridRefs = this.$refs.previewGrid;
//             const gridRef = Array.isArray(gridRefs)
//               ? gridRefs.find((g) => g.$attrs["ref-key"] === groupKey)
//               : gridRefs?.[groupKey];

//             if (gridRef?.selectAll) {
//               gridRef.selectAll(true);
//               console.log(`✅ Auto-selected page rows for: ${groupKey}`);
//             }

//             this.onProductSelectionChange(groupKey, updatedSelection);
//           });
//         });
//       });
//     },

//     async removeSelectedProducts() {
//       const allSelectedIds = Object.values(this.selectedPreviewProducts).flat();
//       console.log("Selected IDs:", allSelectedIds);
      
//       if (!allSelectedIds.length) {
//         this.createNotificationWarning({
//           title: "No Selection",
//           message: "Please select at least one product.",
//         });
//         return;
//       }

//       const httpClient = Shopware.Application.getContainer("init").httpClient;
//       const loginService = Shopware.Service("loginService");
//       const token = loginService.getToken();

//       if (!token) {
//         this.createNotificationError({
//           title: "Auth Error",
//           message: "Authorization token is missing.",
//         });
//         return;
//       }

//       const payload = {
//         productIds: allSelectedIds,
//       };

//       try {
//         const response = await httpClient.post(
//           "/ict-data-cleaner/products/remove",
//           payload,
//           {
//             headers: { Authorization: `Bearer ${token}` },
//           }
//         );

//         this.createNotificationSuccess({
//           title: "Deleted",
//           message: `${response.data.deleted} products deleted successfully`,
//         });

//         this.selectedPreviewProducts = {};
//         this.showPreviewModal = false;
//         await this.previewAction(this.activePreviewKey);
//       } catch (error) {
//         this.createNotificationError({
//           title: "Delete Failed",
//           message: error?.response?.data?.error || "Could not delete products.",
//         });
//       }
//     },
//     allSettings() {
//       return {
//         ...this.productSettings,
//         ...this.customerSettings,
//         ...this.cartSettings,
//         ...this.categorySettings,
//         ...this.promotionSettings,
//         ...this.cmsPageSettings,
//         ...this.reviewSettings,
//         ...this.newsletterSettings,
//         ...this.mediaSettings,
//       };
//     },
//   },
// });


// ict-data-cleaner-index.js
// from here
import template from "./ict-data-cleaner-index.html.twig";
import "./ict-data-cleaner-index.scss";

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register("ict-data-cleaner-index", {
  template,

  inject: [
    "repositoryFactory",
    "systemConfigApiService",
    "ictDataCleanerService",
  ],

  mixins: [Mixin.getByName("notification")],

  data() {
    return {
      isLoading: false,
      activeTab: "products",
      isSaveSuccessful: false,
      isDryRun: true,
      previewResults: null,
      showPreviewModal: false,
      showConfirmModal: false,
      totalItemsToDelete: 0,
      salesChannelId: null,
      config: {},
      previewData: null,
      selectedPreviewProducts: {},
      gridKey: 0,
      gridReady: false,
      pagination: {},
      tabConfigs: {
    products: {
        label: "Products & Variants",
        fields: [
            { key: 'productCleanup.monthsNotSold', label: 'Products not sold in months', type: 'int' },
            { key: 'productCleanup.deleteNeverSold', label: 'Products never sold', type: 'bool' },
            { key: 'productCleanup.monthsDisabled', label: 'Inactive products older than months', type: 'int' },
        ],
    },
    customers: {
        label: "Customers & Accounts",
        fields: [
            { key: 'customerCleanup.guestMonths', label: 'Guest accounts inactive for months', type: 'int' },
            { key: 'customerCleanup.inactiveMonths', label: 'Inactive customers for months', type: 'int' },
        ],
    },
    carts: {
        label: "Orders, Carts & Checkouts",
        fields: [
            { key: 'cartCleanup.abandonedDays', label: 'Abandoned carts older than days', type: 'int' },
            { key: 'orderCleanup.cancelledAgeMonths', label: 'Cancelled orders older than months', type: 'int' },
            { key: 'orderCleanup.oldAgeMonths', label: 'Old transactions older than months', type: 'int' },
        ],
    },
    categories: {
        label: "Categories & Navigation",
        fields: [
            { key: 'categoryCleanup.emptyCategories', label: 'Empty categories', type: 'bool' },
            { key: 'categoryCleanup.noSalesMonths', label: 'Categories with no sales in months', type: 'int' },
        ],
    },
    promotions: {
        label: "Promotions & Discounts",
        fields: [
            { key: 'promotionCleanup.expiredMonths', label: 'Expired promotions older than months', type: 'int' },
            { key: 'promotionCleanup.unusedVoucherMonths', label: 'Unused vouchers older than months', type: 'int' },
            { key: 'promotionCleanup.orphaned', label: 'Orphaned cart rules', type: 'bool' },
        ],
    },
    cmsPages: {
        label: "CMS Content & Pages",
        fields: [
            { key: 'cmsPageCleanup.unpublishedDraftsMonths', label: 'Unpublished CMS drafts older than months', type: 'int' },
        ],
    },
    reviews: {
        label: "Reviews & Ratings",
        fields: [
            { key: 'reviewCleanup.unapprovedDays', label: 'Unapproved reviews older than days', type: 'int' },
        ],
    },
    newsletter: {
        label: "Marketing Lists",
        fields: [
            { key: 'newsletterCleanup.bouncedMonths', label: 'Bounced recipients older than months', type: 'int' },
        ],
    },
    media: {
        label: "Media Management",
        fields: [
            { key: 'mediaCleanup.orphanAgeDays', label: 'Orphaned media after days', type: 'int' },
        ],
    },
},

    };
  },

  created() {
    this.loadSystemConfig();
},

  computed: {
    configDomain() {
      return "IctDataCleanerPro.config";
    },

    paginatedRows() {
      const result = {};
      if (!this.previewData?.items) return result;

      for (const [key, group] of Object.entries(this.previewData.items)) {
        const page = this.pagination?.[key]?.page || 1;
        const limit = this.pagination?.[key]?.limit || 10;
        result[key] = (group.sample || []).slice(
          (page - 1) * limit,
          page * limit
        );
      }

      return result;
    },
  },

  methods: {
    getColumnsForGroup(key) {
      const sample = this.previewData?.items?.[key]?.sample;
      if (!Array.isArray(sample) || sample.length === 0) return [];

      const firstRow = sample[0];
      if (typeof firstRow !== "object" || firstRow === null) return [];

      return Object.keys(firstRow)
        .filter(field => field !== 'id')
        .map(field => ({
          property: field,
          label: this.beautifyLabel(field),
        }));
    },

    beautifyLabel(field) {
      return field.replace(/_/g, " ").replace(/\b\w/g, char => char.toUpperCase());
    },

    setActiveTab(tabKey) {
      this.activeTab = tabKey;
    },

    // previewAction(field) {
    //   this.showPreviewModal = true;
    //   this.gridReady = false;
    //   this.activePreviewKey = field;

    //   const httpClient = Shopware.Application.getContainer("init").httpClient;
    //   const loginService = Shopware.Service("loginService");
    //   const token = loginService.getToken();

    //   const key = `IctDataCleaner.config.${field}`;
    //   const value = this.allSettings()[key];
    //   if (value === undefined) return;
    //   const config = { [key]: value };

    //   httpClient.post("/ict-data-cleaner/preview", config, {
    //     headers: { Authorization: `Bearer ${token}` },
    //   })
    //     .then(response => {
    //         console.log("✅ Preview API response:", response.data);

    //       this.previewData = response.data.data;
    //       this.selectedPreviewProducts = {};
    //       this.pagination = {};

    //       Object.entries(this.previewData.items).forEach(([groupKey, group]) => {
    //         const ids = (group.sample || []).map(p => p.id);
    //         this.$set(this.selectedPreviewProducts, groupKey, ids);
    //         this.$set(this.pagination, groupKey, {
    //           page: 1,
    //           limit: 10,
    //           total: group.count || 0,
    //         });
    //       });

    //       this.gridKey++;

    //       this.$nextTick(() => {
    //         this.gridReady = true;
    //           console.log("✅ gridReady set to true");

    //         this.showPreviewModal = true;

    //         this.$nextTick(() => {
    //           requestAnimationFrame(() => {
    //             const previewGrids = this.$refs.previewGrid || {};

    //             if (Array.isArray(previewGrids)) {
    //               previewGrids.forEach((gridRef, index) => {
    //                 if (gridRef?.selectAll) {
    //                   gridRef.selectAll(true);
    //                 }
    //               });
    //             } else {
    //               Object.keys(this.previewData.items).forEach((key) => {
    //                 const gridRef = previewGrids[key];
    //                 if (gridRef?.selectAll) {
    //                   gridRef.selectAll(true);
    //                 }
    //               });
    //             }
    //           });
    //         });
    //       });
    //     })
    //     .catch(error => {
    //       console.error("Preview request failed:", error);
    //     });
    // },

    previewAction(field) {
    console.log("⚡️ previewAction called with:", field);
    this.showPreviewModal = true;
    this.gridReady = false;
    this.activePreviewKey = field;

    const httpClient = Shopware.Application.getContainer("init").httpClient;
    const loginService = Shopware.Service("loginService");
    const token = loginService.getToken();

    // ✅ Use cleaned config directly
    const value = this.config[field];

    if (value === undefined) {
        console.warn("❌ No config value for", field);
        this.gridReady = false;
        return;
    }

    const key = `IctDataCleaner.config.${field}`; // Backend still expects full key
    const config = { [key]: value };

    console.log("📦 Sending preview config:", config);

    httpClient.post("/ict-data-cleaner/preview", config, {
        headers: { Authorization: `Bearer ${token}` },
    })
    .then(response => {
        const data = response.data?.data;

        if (!data?.items || !Object.keys(data.items).length) {
            this.gridReady = false;
            this.createNotificationWarning({
                title: "No Preview Data",
                message: "There is nothing to preview based on the current config.",
            });
            return;
        }

        this.previewData = data;
        this.selectedPreviewProducts = {};
        this.pagination = {};

        Object.entries(this.previewData.items).forEach(([groupKey, group]) => {
            const ids = (group.sample || []).map(p => p.id);
            this.$set(this.selectedPreviewProducts, groupKey, ids);
            this.$set(this.pagination, groupKey, {
                page: 1,
                limit: 10,
                total: group.count || 0,
            });
        });

        this.gridKey++;

        this.$nextTick(() => {
            this.gridReady = true;

            this.$nextTick(() => {
                requestAnimationFrame(() => {
                    const previewGrids = this.$refs.previewGrid || {};

                    if (Array.isArray(previewGrids)) {
                        previewGrids.forEach(gridRef => gridRef?.selectAll?.(true));
                    } else {
                        Object.keys(this.previewData.items).forEach(key => {
                            const gridRef = previewGrids[key];
                            gridRef?.selectAll?.(true);
                        });
                    }
                });
            });
        });
    })
    .catch(error => {
        console.error("❌ Preview request failed:", error?.response || error);
        this.createNotificationError({
            title: "Preview Error",
            message: error?.response?.data?.errors?.[0]?.detail || "Preview failed.",
        });
    });
},

    async removeSelectedItems(entityKey) {
      const allSelectedIds = Object.values(this.selectedPreviewProducts).flat();

      if (!allSelectedIds.length) {
        this.createNotificationWarning({
          title: "No Selection",
          message: "Please select at least one item.",
        });
        return;
      }

      const httpClient = Shopware.Application.getContainer("init").httpClient;
      const loginService = Shopware.Service("loginService");
      const token = loginService.getToken();

      const payload = {
        [`${entityKey}Ids`]: allSelectedIds,
      };

      try {
        const response = await httpClient.post(
          `/ict-data-cleaner/${entityKey}/remove`,
          payload,
          { headers: { Authorization: `Bearer ${token}` } }
        );

        this.createNotificationSuccess({
          title: "Deleted",
          message: `items deleted successfully`,
        });

        this.selectedPreviewProducts = {};
        this.showPreviewModal = false;
        await this.previewAction(this.activePreviewKey);
      } catch (error) {
        this.createNotificationError({
          title: "Delete Failed",
          message: error?.response?.data?.error || `Could not delete ${entityKey} items.`,
        });
      }
    },

    onProductSelectionChange(groupKey, selectedIds) {
      this.$set(this.selectedPreviewProducts, groupKey, selectedIds);
    },

    onPageChange({ page, limit }, groupKey) {
      this.$set(this.pagination, groupKey, {
        page,
        limit,
        total: this.previewData?.items?.[groupKey]?.sample?.length || 0,
      });

      this.gridReady = false;

      this.$nextTick(() => {
        this.gridKey++;

        this.$nextTick(() => {
          this.gridReady = true;

          this.$nextTick(() => {
            const group = this.previewData?.items?.[groupKey];
            if (!group) return;

            const currentPageItems = (group.sample || [])
              .slice((page - 1) * limit, page * limit)
              .map((p) => p.id);

            const currentSelected = Array.isArray(
              this.selectedPreviewProducts[groupKey]
            ) ? this.selectedPreviewProducts[groupKey] : [];

            const updatedSelection = [...new Set([...currentSelected, ...currentPageItems])];

            this.$set(this.selectedPreviewProducts, groupKey, updatedSelection);

            const gridRefs = this.$refs.previewGrid;
            const gridRef = Array.isArray(gridRefs)
              ? gridRefs.find((g) => g.$attrs["ref-key"] === groupKey)
              : gridRefs?.[groupKey];

            if (gridRef?.selectAll) {
              gridRef.selectAll(true);
            }

            this.onProductSelectionChange(groupKey, updatedSelection);
          });
        });
      });
    },

    allSettings() {
      return {
        ...this.productSettings,
        ...this.customerSettings,
        ...this.cartSettings,
        ...this.categorySettings,
        ...this.promotionSettings,
        ...this.cmsPageSettings,
        ...this.reviewSettings,
        ...this.newsletterSettings,
        ...this.mediaSettings,
            ...this.systemLogSettings, // ✅ missing here

      };
    },
 getConfigValue(key, type = 'int') {
  const value = this.config[key]; // ✅ No more prefix

  if (type === 'bool') {
    return value ? 'Yes' : 'No';
  }

  return value;
},
loadSystemConfig() {
    this.systemConfigApiService.getValues('IctDataCleanerPro.config').then(response => {
        const formattedConfig = {};

        for (const fullKey in response) {
            const value = response[fullKey];

            // Remove prefix
            let key = fullKey.replace('IctDataCleanerPro.config.', '');

            // Match group name and rest
            const match = key.match(/^([a-zA-Z]+Cleanup|[a-zA-Z]+VariantCleanup|[a-zA-Z]+ThumbnailCleanup|cacheCleanup|customFieldSetCleanup|systemLogCleanup|reviewCleanup|newsletterCleanup|mediaCleanup|promotionCleanup|orderCleanup|transactionCleanup|cmsPageCleanup|categoryCleanup|productCleanup|customerCleanup|cartRuleCleanup|cartCleanup)(.+)$/);

            if (match) {
                const group = match[1]; // e.g. productCleanup
                const rest = match[2];  // e.g. MonthsNotSold

                // Lowercase the first letter after the dot
                const restFormatted = rest.charAt(0).toLowerCase() + rest.slice(1);

                const finalKey = `${group}.${restFormatted}`;
                formattedConfig[finalKey] = value;
            } else {
                // fallback for keys like: dryRunMode → dry.run.mode
                const fallbackKey = key.replace(/([a-z])([A-Z])/g, '$1.$2').toLowerCase();
                formattedConfig[fallbackKey] = value;
            }
        }

        this.config = formattedConfig;
        console.log("✅ Final cleaned config:", this.config);
    });
}



  },
});

