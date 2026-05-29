import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'GeoIp',
  description: 'Resolve visitor geolocation from IP addresses in Laravel.',
  base: '/geo-ip/',
  themeConfig: {
    nav: [
      { text: 'Guide', link: '/getting-started/installation' },
      { text: 'GitHub', link: 'https://github.com/arielmejiadev/geo-ip' },
    ],
    sidebar: [
      {
        text: 'Getting Started',
        items: [
          { text: 'Installation', link: '/getting-started/installation' },
          { text: 'Configuration', link: '/getting-started/configuration' },
        ],
      },
      {
        text: 'Usage',
        items: [
          { text: 'Quick Lookups', link: '/usage/quick-lookups' },
          { text: 'Fluent API', link: '/usage/fluent-api' },
          { text: 'Conditional Logic', link: '/usage/conditional-logic' },
          { text: 'Serialization', link: '/usage/serialization' },
        ],
      },
      {
        text: 'Drivers',
        items: [
          { text: 'Overview', link: '/drivers/overview' },
          { text: 'DB-IP Lite', link: '/drivers/dbip' },
          { text: 'MaxMind', link: '/drivers/maxmind' },
          { text: 'ip-api', link: '/drivers/ip-api' },
          { text: 'ipinfo', link: '/drivers/ipinfo' },
        ],
      },
      {
        text: 'Advanced',
        items: [
          { text: 'Caching', link: '/advanced/caching' },
          { text: 'Custom Drivers', link: '/advanced/custom-drivers' },
          { text: 'Macros', link: '/advanced/macros' },
          { text: 'Testing', link: '/advanced/testing' },
          { text: 'Artisan Command', link: '/advanced/artisan-command' },
        ],
      },
    ],
    socialLinks: [
      { icon: 'github', link: 'https://github.com/arielmejiadev/geo-ip' },
    ],
  },
})
