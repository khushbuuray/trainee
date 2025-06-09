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
//       total: 0, // Total number of items
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
//       selectedPreviewProducts: [],
//       allSelected: true,
//       gridKey: 0,
//       gridReady: false,
//       productSettings: {
//         "IctDataCleaner.config.productCleanup.monthsNotSold": 6, // Default to 6 months
//         "IctDataCleaner.config.productCleanup.deleteNeverSold": false,
//         "IctDataCleaner.config.productCleanup.monthsDisabled": 6,
//         "IctDataCleaner.config.productVariantCleanupZeroStockMonths": 6,
//       },
//     };
//   },

//   computed: {
//     configDomain() {
//       return "IctDataCleanerPro.config";
//     },

//     // Paginated products
//     paginatedProducts() {
//       // const all = this.previewData?.items?.products_never_sold?.sample || [];
//       // const start = (this.currentPage - 1) * this.limit;
//       // const paginatedItems = all.slice(start, start + this.limit);

//       // // Convert proxy objects to plain objects for easier manipulation
//       // const plainProducts = paginatedItems.map((product) => {
//       //   return JSON.parse(JSON.stringify(product)); // Create a deep copy
//       // });

//       // console.log("Paginated Products:", plainProducts); // Now logging plain objects
//       // return plainProducts;
//       const all = this.previewData?.items?.products_never_sold?.sample || [];
//       const start = (this.currentPage - 1) * this.limit;
//       return all.slice(start, start + this.limit);
//     },
//   },

//   methods: {
//     // Preview action
//     previewAction(field) {
//       this.gridReady = false;

//       // this.selectedPreviewProducts = {};
//       // this.paginatedProducts.forEach((product) => {
//       //   if (product.id) {
//       //     this.selectedPreviewProducts[product.id] = product;
//       //   }
//       // });
//       this.selectedPreviewProducts = this.paginatedProducts.filter(
//         (p) => p?.id
//       );

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
//           this.previewData = response.data.data; // Update the data
//           this.total = this.previewData?.items?.products_never_sold?.count || 0;

//           console.log("Preview Data:", this.previewData);

//           this.$nextTick(() => {
//             this.gridReady = false;
//             this.gridKey += 1; // Force grid re-render

//             // Set selectedPreviewProducts to current page's products
//             this.selectedPreviewProducts = this.paginatedProducts.slice();

//             this.gridReady = true; // Grid can now render with preselected rows
//             this.showPreviewModal = true;

//             console.log(
//               "All products preselected:",
//               this.selectedPreviewProducts
//             );
//           });
//         })
//         .catch((error) => {
//           console.error(
//             "Request error:",
//             error.response?.data?.errors ?? error.message
//           );
//         });
//     },
//     onProductSelectionChange(selectedItems) {
//       // this.selectedPreviewProducts = selectedItems; // Update the selected items
//       // console.log("Selected selectedPreviewProducts:", this.selectedPreviewProducts);
//     },
//     // Handle page change
//     onPageChange(page) {
//       // this.currentPage = page;
//       // this.gridKey += 1; // forces grid to refresh
//       // console.log("Page Changed:", this.currentPage);
//       //  this.$nextTick(() => {
//       this.selectAllProducts(); // Ensure selection updates with new page
//       // });
//     },
//     openModal() {
//       this.showPreviewModal = true;
//       // this.selectedPreviewProducts = this.paginatedProducts.map(
//       //   (product) => product.id
//       // );
//       this.selectedPreviewProducts = this.paginatedProducts.filter(
//         (p) => p?.id
//       );
//     },

//     // Handle product selection
//     selectAllProducts() {
//       const items = this.paginatedProducts || [];

//       // const selected = {};
//       // items.forEach((product) => {
//       //   if (product?.id) {
//       //     // selected[product.id] = product;
//       //     selected[product.id.toString()] = product;
//       //   }
//       // });

//       this.selectedPreviewProducts = items.filter((product) => product?.id);
//       console.log("Selected All Products():", this.selectedPreviewProducts);
//     },
//   },
// });

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
//       selectedPreviewProducts: [],
//       allSelected: true,
//       gridKey: 0,
//       gridReady: false,
//       manuallySelectedItems: [],
//       productSettings: {
//         "IctDataCleaner.config.productCleanup.monthsNotSold": 6,
//         "IctDataCleaner.config.productCleanup.deleteNeverSold": false,
//         "IctDataCleaner.config.productCleanup.monthsDisabled": 6,
//         "IctDataCleaner.config.productVariantCleanupZeroStockMonths": 6,
//       },
//     };
//   },

//   computed: {
//     configDomain() {
//       return "IctDataCleanerPro.config";
//     },

//     paginatedProducts() {
//       const all = this.previewData?.items?.products_never_sold?.sample || [];
//       const start = (this.currentPage - 1) * this.limit;
//       return all.slice(start, start + this.limit);
//     },
//   },

//   // methods: {
//   //   previewAction(field) {
//   //     this.gridReady = false;

//   //     const httpClient = Shopware.Application.getContainer("init").httpClient;
//   //     const loginService = Shopware.Service("loginService");
//   //     const token = loginService.getToken();

//   //     if (!token) {
//   //       console.error("Auth token not found.");
//   //       return;
//   //     }

//   //     const config = {
//   //       [field]: this.productSettings[`IctDataCleaner.config.${field}`],
//   //     };

//   //     httpClient
//   //       .post("/ict-data-cleaner/preview", config, {
//   //         headers: { Authorization: `Bearer ${token}` },
//   //       })
//   //       .then((response) => {
//   //         this.previewData = response.data.data;
//   //         this.total = this.previewData?.items?.products_never_sold?.count || 0;

//   //         this.$nextTick(() => {
//   //           this.gridReady = false;
//   //           this.gridKey += 1;

//   //           // Set selectedPreviewProducts to an array of product IDs
//   //           this.selectedPreviewProducts = this.paginatedProducts
//   //             .filter(p => p?.id)
//   //             .map(p => p.id);

//   //           this.gridReady = true;
//   //           this.showPreviewModal = true;

//   //           console.log("All products preselected (IDs):", this.selectedPreviewProducts);
//   //         });
//   //       })
//   //       .catch((error) => {
//   //         console.error(
//   //           "Request error:",
//   //           error.response?.data?.errors ?? error.message
//   //         );
//   //       });
//   //   },

//   //   onProductSelectionChange(selectedItems) {
//   //     // selectedItems is an array of product IDs (strings)
//   //     this.selectedPreviewProducts = selectedItems;
//   //     console.log("Selected product IDs:", this.selectedPreviewProducts);
//   //   },

//   //   onPageChange(page) {
//   //     this.currentPage = page;
//   //     this.gridKey += 1; // force grid refresh
//   //     this.$nextTick(() => {
//   //       this.selectAllProducts();
//   //     });
//   //   },

//   //   openModal() {
//   //     this.showPreviewModal = true;
//   //     this.selectedPreviewProducts = this.paginatedProducts
//   //       .filter(p => p?.id)
//   //       .map(p => p.id);
//   //   },

//   //   selectAllProducts() {
//   //     const items = this.paginatedProducts || [];
//   //     this.selectedPreviewProducts = items
//   //       .filter(p => p?.id)
//   //       .map(p => p.id);

//   //     console.log("Selected All Product IDs:", this.selectedPreviewProducts);
//   //   },
//   // },
//   methods: {
//  previewAction(field) {
//   this.gridReady = false;

//   const httpClient = Shopware.Application.getContainer("init").httpClient;
//   const loginService = Shopware.Service("loginService");
//   const token = loginService.getToken();

//   if (!token) {
//     console.error("Auth token not found.");
//     return;
//   }

//   const config = {
//     [field]: this.productSettings[`IctDataCleaner.config.${field}`],
//   };

//   httpClient
//     .post("/ict-data-cleaner/preview", config, {
//       headers: { Authorization: `Bearer ${token}` },
//     })
//     .then((response) => {
//       this.previewData = response.data.data;
//       this.total = this.previewData?.items?.products_never_sold?.count || 0;

//       // Extract all product IDs
//       const allItems = this.previewData?.items?.products_never_sold?.sample || [];
//       this.selectedPreviewProducts = this.paginatedProducts
//   .filter(p => p?.id)
//   .map(p => p.id);

//       // Force grid to reload
//       this.gridKey += 1;

//       // Wait for modal and grid to fully mount
//       this.$nextTick(() => {
//         this.gridReady = true;
//         this.showPreviewModal = true;

//         // Another $nextTick + timeout ensures rendering is complete
//         this.$nextTick(() => {
//           setTimeout(() => {
//             const gridRef = this.$refs.previewGrid;
//             if (gridRef && typeof gridRef.selectAll === 'function') {
//               gridRef.selectAll(true);
//               console.log("Programmatically selected all after render.");
//             }
//           }, 50); // 50–100ms is usually enough
//         });
//       });

//     })
//     .catch((error) => {
//       console.error("Request error:", error.response?.data?.errors ?? error.message);
//     });
// },


//     toggleSelection(itemId) {
//         const index = this.manuallySelectedItems.indexOf(itemId);
//         if (index > -1) {
//             this.manuallySelectedItems.splice(index, 1); // Uncheck
//         } else {
//             this.manuallySelectedItems.push(itemId); // Check
//         }
//     },

//   onProductSelectionChange(selectedItems) {
//     // selectedItems is array of IDs (strings)
//     this.selectedPreviewProducts = selectedItems;
//     console.log("Selected product IDs:", this.selectedPreviewProducts);
//   },

//   onPageChange(page) {
//     this.currentPage = page;
//     this.gridKey += 1; // force refresh
//     this.$nextTick(() => {
//       // Set selection for new page
//       this.selectedPreviewProducts = this.paginatedProducts
//         .filter(p => p?.id)
//         .map(p => p.id);

//       console.log("Page changed, selected IDs:", this.selectedPreviewProducts);
//     });
//   },
// },

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
      currentPage: 1,
      limit: 10,
      total: 0,
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
      selectedPreviewProducts: [],
      gridKey: 0,
      gridReady: false,
      productSettings: {
        "IctDataCleaner.config.productCleanup.monthsNotSold": 6,
        "IctDataCleaner.config.productCleanup.deleteNeverSold": false,
        "IctDataCleaner.config.productCleanup.monthsDisabled": 6,
        "IctDataCleaner.config.productVariantCleanupZeroStockMonths": 6,
      },
    };
  },

  computed: {
    configDomain() {
      return "IctDataCleanerPro.config";
    },

    paginatedProducts() {
      const all = this.previewData?.items?.products_never_sold?.sample || [];
      const start = (this.currentPage - 1) * this.limit;
      return all.slice(start, start + this.limit);
    },
  },

  methods: {
    previewAction(field) {
      this.gridReady = false;
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
          this.total = this.previewData?.items?.products_never_sold?.count || 0;

          this.gridKey += 1;

          this.$nextTick(() => {
            this.gridReady = true;
            this.showPreviewModal = true;

            // Auto-select all products on the first page
            this.selectedPreviewProducts = this.paginatedProducts
              .filter(p => p?.id)
              .map(p => p.id);

            this.$nextTick(() => {
              setTimeout(() => {
                const gridRef = this.$refs.previewGrid;
                if (gridRef && typeof gridRef.selectAll === 'function') {
                  gridRef.selectAll(true);
                  console.log("Programmatically selected all rows after render.");
                }
              }, 50);
            });
          });
        })
        .catch((error) => {
          console.error("Preview request failed:", error.response?.data?.errors ?? error.message);
        });
    },

    onProductSelectionChange(selectedIds) {
      this.selectedPreviewProducts = selectedIds;
      console.log("Selected product IDs:", this.selectedPreviewProducts);
    },

    onPageChange(page) {
      this.currentPage = page;
      this.gridKey += 1;

      this.$nextTick(() => {
        this.selectedPreviewProducts = this.paginatedProducts
          .filter(p => p?.id)
          .map(p => p.id);

        console.log("Page changed, reselected product IDs:", this.selectedPreviewProducts);
      });
    },

    openModal() {
      this.showPreviewModal = true;
      this.selectedPreviewProducts = this.paginatedProducts
        .filter(p => p?.id)
        .map(p => p.id);
    },

    selectAllProducts() {
      this.selectedPreviewProducts = this.paginatedProducts
        .filter(p => p?.id)
        .map(p => p.id);

      console.log("Manually selected all products on page:", this.selectedPreviewProducts);
    },
  },
});


