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
          this.total = this.previewData?.items?.products_never_sold?.count || 0;

          this.gridKey += 1;

          this.$nextTick(() => {
            this.gridReady = true;
            this.showPreviewModal = true;

            // Auto-select all products on the first page
            this.selectedPreviewProducts = this.paginatedProducts
              .filter((p) => p?.id)
              .map((p) => p.id);

            this.$nextTick(() => {
              setTimeout(() => {
                const gridRef = this.$refs.previewGrid;
                if (gridRef && typeof gridRef.selectAll === "function") {
                  gridRef.selectAll(true);
                  console.log(
                    "Programmatically selected all rows after render."
                  );
                }
              }, 50);
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

    onProductSelectionChange(selectedIds) {
      this.selectedPreviewProducts = selectedIds;
      console.log("Selected product IDs:", this.selectedPreviewProducts);
    },

    onPageChange(page) {
      this.currentPage = page;
      this.gridKey += 1;

      this.$nextTick(() => {
        this.selectedPreviewProducts = this.paginatedProducts
          .filter((p) => p?.id)
          .map((p) => p.id);

        console.log(
          "Page changed, reselected product IDs:",
          this.selectedPreviewProducts
        );
      });
    },

    openModal() {
      this.showPreviewModal = true;
      this.selectedPreviewProducts = this.paginatedProducts
        .filter((p) => p?.id)
        .map((p) => p.id);
    },

    selectAllProducts() {
      this.selectedPreviewProducts = this.paginatedProducts
        .filter((p) => p?.id)
        .map((p) => p.id);

      console.log(
        "Manually selected all products on page:",
        this.selectedPreviewProducts
      );
    },

    async removeSelectedProducts() {
      if (
        !this.selectedPreviewProducts ||
        this.selectedPreviewProducts.length === 0
      ) {
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
        productIds: this.selectedPreviewProducts,
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
          message: `${
            response.data.deleted
          } products were deleted`,
        });

        // Clear selection and optionally refresh table
        this.selectedPreviewProducts = [];
      this.showPreviewModal = false; // hide
      await this.previewAction(this.activePreviewKey); // re-show & reload
      } catch (error) {
        this.createNotificationError({
          title: "Delete Failed",
          message: error?.response?.data?.error || "Could not delete products.",
        });
      }
    },
  },
});
