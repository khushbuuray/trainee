import template from "./ict-data-cleaner-results.html.twig"
import "./ict-data-cleaner-results.scss"

const { Component } = Shopware

Component.register("ict-data-cleaner-results", {
  template,

  props: {
    results: {
      type: Object,
      required: true,
    },
  },

  computed: {
    handlers() {
      return Object.values(this.results)
    },

    totalItems() {
      let total = 0
      this.handlers.forEach((handler) => {
        if (handler.items) {
          Object.values(handler.items).forEach((item) => {
            total += item.count || 0
          })
        }
      })
      return total
    },
  },
})
