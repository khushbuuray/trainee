import "./page/ict-data-cleaner-index"
import "./page/ict-data-cleaner-logs"
import "./component/ict-data-cleaner-preview"
import "./component/ict-data-cleaner-results"
import deDE from "./snippet/de-DE.json"
import enGB from "./snippet/en-GB.json"

const { Module } = Shopware

Module.register("ict-data-cleaner", {
  type: "plugin",
  name: "data-cleaner",
  title: "ict-data-cleaner.general.title",
  description: "ict-data-cleaner.general.description",
  color: "#57D9A3",
  icon: "regular-trash",

  snippets: {
    "de-DE": deDE,
    "en-GB": enGB,
  },

  routes: {
    index: {
      component: "ict-data-cleaner-index",
      path: "index",
      meta: {
        parentPath: "sw.settings.index",
        privilege: "system.system_config",
      },
    },
    logs: {
      component: "ict-data-cleaner-logs",
      path: "logs",
      meta: {
        parentPath: "ict.data.cleaner.index",
        privilege: "system.system_config",
      },
    },
  },

  settingsItem: {
    group: "system",
    to: "ict.data.cleaner.index",
    icon: "regular-trash",
    privilege: "system.system_config",
  },
})
