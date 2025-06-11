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
//       currentPage: 1,
//       limit: 10,
//       total: 0,
//       isLoading: false,
//       isSaveSuccessful: false,
//       isDryRun: true,
//       previewResults: null,
//       showPreviewModal: false,
//       showConfirmModal: false,
//       totalItemsToDelete: 0,
//       salesChannelId: null,
//       config: null,
//       previewData: null,
//       selectedPreviewProducts: {}, // per group
//       gridKey: 0,
//       gridReady: false,
//       productSettings: {
//         "IctDataCleaner.config.productCleanup.monthsNotSold": 6,
//         "IctDataCleaner.config.productCleanup.deleteNeverSold": false,
//         "IctDataCleaner.config.productCleanup.monthsDisabled": 6,
//         "IctDataCleaner.config.productVariantCleanupZeroStockMonths": 6,
//         "IctDataCleaner.config.enableScheduler": false
//       },
//     };
//   },

//   computed: {
//     configDomain() {
//       return "IctDataCleanerPro.config";
//     },
//   },

//   methods: {
//     previewAction(field) {
//       this.gridReady = false;
//       this.activePreviewKey = field;

//       const httpClient = Shopware.Application.getContainer("init").httpClient;
//       const loginService = Shopware.Service("loginService");
//       const token = loginService.getToken();

//       if (!token) {
//         console.error("Auth token not found.");
//         return;
//       }

//       const config = {
//         [field]: this.productSettings[`IctDataCleaner.config.${field}`],
//       };

//       httpClient
//         .post("/ict-data-cleaner/preview", config, {
//           headers: { Authorization: `Bearer ${token}` },
//         })
//         .then((response) => {
//           this.previewData = response.data.data;
//           this.selectedPreviewProducts = {};

//           Object.entries(this.previewData.items).forEach(([groupKey, group]) => {
//             const ids = (group.sample || []).map(p => p.id);
//             this.$set(this.selectedPreviewProducts, groupKey, ids);
//           });

//           this.currentPage = 1;
//           this.total = this.previewData?.items?.[field]?.count || 0;
//           this.gridKey += 1;

//           this.$nextTick(() => {
//             this.gridReady = true;
//             this.showPreviewModal = true;

//             this.$nextTick(() => {
//               requestAnimationFrame(() => {
//                 const previewGrids = this.$refs.previewGrid || {};

//                 // Vue 2: $refs.previewGrid is an array if multiple elements have the same ref
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
//                       console.warn(`⚠️ Grid ref not found for: ${key}`, previewGrids);
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
//       this.currentPage = page;
//       this.limit = limit;
//       this.gridReady = false;

//       this.$nextTick(() => {
//         this.gridKey += 1;

//         this.$nextTick(() => {
//           this.gridReady = true;

//           setTimeout(() => {
//             const group = this.previewData?.items?.[groupKey];
//             if (!group) return;

//             const currentPageItems = (group.sample || [])
//               .slice((page - 1) * limit, page * limit)
//               .map(p => p.id);

//             const currentSelected = Array.isArray(this.selectedPreviewProducts[groupKey])
//               ? this.selectedPreviewProducts[groupKey]
//               : [];

//             const updatedSelection = [
//               ...new Set([
//                 ...currentSelected.filter(id => !currentPageItems.includes(id)),
//                 ...currentPageItems,
//               ])
//             ];

//             this.$set(this.selectedPreviewProducts, groupKey, updatedSelection);

//             const gridRefs = this.$refs.previewGrid;
//             const gridRef = Array.isArray(gridRefs) ? gridRefs.find(g => g.$attrs["ref-key"] === groupKey) : gridRefs?.[groupKey];

//             if (gridRef?.selectAll) {
//               gridRef.selectAll(true);
//             }

//             this.onProductSelectionChange(groupKey, updatedSelection);
//           }, 50);
//         });
//       });
//     },

//     async removeSelectedProducts() {
//       const allSelectedIds = Object.values(this.selectedPreviewProducts).flat();

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
//         await this.previewAction(this.activePreviewKey); // refresh
//       } catch (error) {
//         this.createNotificationError({
//           title: "Delete Failed",
//           message: error?.response?.data?.error || "Could not delete products.",
//         });
//       }
//     },
//   },
// });
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
      isSaveSuccessful: false,
      isDryRun: true,
      previewResults: null,
      showPreviewModal: false,
      showConfirmModal: false,
      totalItemsToDelete: 0,
      salesChannelId: null,
      config: null,
      previewData: null,
      selectedPreviewProducts: {},
      gridKey: 0,
      gridReady: false,
      pagination: {},
      productSettings: {
        "IctDataCleaner.config.productCleanup.monthsNotSold": 6,
        "IctDataCleaner.config.productCleanup.deleteNeverSold": false,
        "IctDataCleaner.config.productCleanup.monthsDisabled": 6,
        "IctDataCleaner.config.productVariantCleanup.zeroStockMonths": 6,
      },
    };
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
      result[key] = (group.sample || []).slice((page - 1) * limit, page * limit);
    }

    return result;
  }
  },

  methods: {
    previewAction(field) {
      this.gridReady = false;
      this.activePreviewKey = field;

      const httpClient = Shopware.Application.getContainer("init").httpClient;
      const loginService = Shopware.Service("loginService");
      const token = loginService.getToken();

      if (!token) {
        console.error("Auth token not found.");
        return;
      }

      const config = {
        [field]: this.productSettings[`IctDataCleaner.config.${field}`],
      };

      httpClient
        .post("/ict-data-cleaner/preview", config, {
          headers: { Authorization: `Bearer ${token}` },
        })
        .then((response) => {
          this.previewData = response.data.data;
          this.selectedPreviewProducts = {};
          this.pagination = {};

          Object.entries(this.previewData.items).forEach(([groupKey, group]) => {
            const ids = (group.sample || []).map(p => p.id);
            this.$set(this.selectedPreviewProducts, groupKey, ids);
            this.$set(this.pagination, groupKey, {
              page: 1,
              limit: 10,
              total: group.count || 0
            });
          });

          this.gridKey += 1;

          this.$nextTick(() => {
            this.gridReady = true;
            this.showPreviewModal = true;

            this.$nextTick(() => {
              requestAnimationFrame(() => {
                const previewGrids = this.$refs.previewGrid || {};

                if (Array.isArray(previewGrids)) {
                  previewGrids.forEach((gridRef, index) => {
                    if (gridRef?.selectAll) {
                      gridRef.selectAll(true);
                      console.log(`✅ Auto-selected all rows in grid ${index}`);
                    }
                  });
                } else {
                  Object.keys(this.previewData.items).forEach((key) => {
                    const gridRef = previewGrids[key];
                    if (gridRef?.selectAll) {
                      gridRef.selectAll(true);
                      console.log(`✅ Auto-selected all rows in grid: ${key}`);
                    } else {
                      console.warn(`⚠️ Grid ref not found for: ${key}`, previewGrids);
                    }
                  });
                }
              });
            });
          });
        })
        .catch((error) => {
          console.error(
            "Preview request failed:",
            error.response?.data?.errors ?? error.message
          );
        });
    },

    onProductSelectionChange(groupKey, selectedIds) {
      this.$set(this.selectedPreviewProducts, groupKey, selectedIds);
      console.log(`Selected IDs for ${groupKey}:`, selectedIds);
    },

   onPageChange({ page, limit }, groupKey) {
  // Update pagination state
  if (!this.pagination) this.pagination = {};
  this.$set(this.pagination, groupKey, {
    page,
    limit,
    total: this.previewData?.items?.[groupKey]?.sample?.length || 0,
  });

  this.gridReady = false;

  this.$nextTick(() => {
    this.gridKey += 1;

    this.$nextTick(() => {
      this.gridReady = true;

      this.$nextTick(() => {
        // Make sure this group exists
        const group = this.previewData?.items?.[groupKey];
        if (!group) return;

        // Slice current page sample
        const currentPageItems = (group.sample || [])
          .slice((page - 1) * limit, page * limit)
          .map(p => p.id);

        // Existing selection for this group
        const currentSelected = Array.isArray(this.selectedPreviewProducts[groupKey])
          ? this.selectedPreviewProducts[groupKey]
          : [];

        // Merge old selection + new page items (prevent duplicates)
        const updatedSelection = [
          ...new Set([
            ...currentSelected,
            ...currentPageItems,
          ]),
        ];

        this.$set(this.selectedPreviewProducts, groupKey, updatedSelection);

        // Select all visible rows in the current page
        const gridRefs = this.$refs.previewGrid;
        const gridRef = Array.isArray(gridRefs)
          ? gridRefs.find(g => g.$attrs["ref-key"] === groupKey)
          : gridRefs?.[groupKey];

        if (gridRef?.selectAll) {
          gridRef.selectAll(true);
          console.log(`✅ Auto-selected page rows for: ${groupKey}`);
        }

        this.onProductSelectionChange(groupKey, updatedSelection);
      });
    });
  });
},


    async removeSelectedProducts() {
      const allSelectedIds = Object.values(this.selectedPreviewProducts).flat();

      if (!allSelectedIds.length) {
        this.createNotificationWarning({
          title: "No Selection",
          message: "Please select at least one product.",
        });
        return;
      }

      const httpClient = Shopware.Application.getContainer("init").httpClient;
      const loginService = Shopware.Service("loginService");
      const token = loginService.getToken();

      if (!token) {
        this.createNotificationError({
          title: "Auth Error",
          message: "Authorization token is missing.",
        });
        return;
      }

      const payload = {
        productIds: allSelectedIds,
      };

      try {
        const response = await httpClient.post(
          "/ict-data-cleaner/products/remove",
          payload,
          {
            headers: { Authorization: `Bearer ${token}` },
          }
        );

        this.createNotificationSuccess({
          title: "Deleted",
          message: `${response.data.deleted} products deleted successfully`,
        });

        this.selectedPreviewProducts = {};
        this.showPreviewModal = false;
        await this.previewAction(this.activePreviewKey);
      } catch (error) {
        this.createNotificationError({
          title: "Delete Failed",
          message: error?.response?.data?.error || "Could not delete products.",
        });
      }
    },
  },
});

