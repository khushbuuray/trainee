import template from "./ict-data-cleaner-logs.html.twig"
import "./ict-data-cleaner-logs.scss"

const { Component, Mixin } = Shopware
const { Criteria } = Shopware.Data

/**
 * @private
 * @package services-settings
 */
Component.register("ict-data-cleaner-logs", {
  template,

  inject: ["repositoryFactory", "httpClient"],

  mixins: [Mixin.getByName("notification")],

  data() {
    return {
      isLoading: false,
      logs: [],
      total: 0,
      limit: 25,
      page: 1,
    }
  },

  metaInfo() {
    return {
      title: this.$tc("ict-data-cleaner.general.logsTab"),
    }
  },

  computed: {
    columns() {
      return [
        {
          property: "runAt",
          label: this.$tc("ict-data-cleaner.logs.columnDate"),
          routerLink: "ict.data.cleaner.logs.detail",
          allowResize: true,
          primary: true,
        },
        {
          property: "trigger",
          label: this.$tc("ict-data-cleaner.logs.columnTrigger"),
          allowResize: true,
        },
        {
          property: "mode",
          label: this.$tc("ict-data-cleaner.logs.columnMode"),
          allowResize: true,
        },
        {
          property: "results",
          label: this.$tc("ict-data-cleaner.logs.columnResults"),
          allowResize: true,
        },
      ]
    },
  },

  created() {
    this.createdComponent()
  },

  methods: {
    createdComponent() {
      this.loadLogs()
    },

    loadLogs() {
      this.isLoading = true

      this.httpClient
        .get(`${Shopware.Context.api.apiPath}/ict-data-cleaner/logs?page=${this.page}&limit=${this.limit}`, {
          headers: this.httpClient.getBasicHeaders(),
        })
        .then((response) => {
          this.logs = response.data.data.items
          this.total = response.data.data.total
        })
        .catch((error) => {
          this.createNotificationError({
            title: this.$tc("global.default.error"),
            message: error.message,
          })
        })
        .finally(() => {
          this.isLoading = false
        })
    },

    onPageChange({ page, limit }) {
      this.page = page
      this.limit = limit
      this.loadLogs()
    },

    formatResults(results) {
      if (!results) {
        return "-"
      }

      let totalItems = 0
      results.forEach((result) => {
        if (result.items) {
          Object.values(result.items).forEach((item) => {
            totalItems += item.count || 0
          })
        }
      })

      return totalItems.toString()
    },
  },
})
