import template from "./ict-data-cleaner-preview.html.twig"
import "./ict-data-cleaner-preview.scss"

const { Component } = Shopware

Component.register("ict-data-cleaner-preview", {
  template,

  props: {
    results: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      expandedHandlers: {},
    }
  },

  computed: {
    handlers() {
      return Object.values(this.results)
    },
  },

  methods: {
    toggleHandler(handlerName) {
      this.$set(this.expandedHandlers, handlerName, !this.expandedHandlers[handlerName])
    },

    isHandlerExpanded(handlerName) {
      return !!this.expandedHandlers[handlerName]
    },

    getTotalItemsForHandler(handler) {
      let total = 0
      if (handler.items) {
        Object.values(handler.items).forEach((item) => {
          total += item.count || 0
        })
      }
      return total
    },
  },
})
