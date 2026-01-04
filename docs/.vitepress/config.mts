import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Mora by SenkuLabs",
  description: "Modular Laravel with native artisan conventions.",
  head: [
    [
      'meta',
      { property: 'og:type', content: 'website' }
    ],
    [
      'meta',
      { property: 'og:title', content: 'Mora by SenkuLabs' }
    ],
    [
      'meta',
      { property: 'og:description', content: 'Modular Laravel with native artisan conventions.' }
    ],
    [
      'meta',
      { property: 'og:url', content: 'https://mora.senkulabs.net' }
    ],
    [
      'meta',
      { property: 'twitter:card', content: 'summary' }
    ],
    [
      'meta',
      { property: 'twitter:title', content: 'Mora by SenkuLabs' }
    ],
    [
      'meta',
      { property: 'twitter:description', content: 'Modular Laravel with native artisan conventions.' }
    ],
  ],
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Home', link: '/' },
      { text: 'What is Mora?', link: '/what-is-mora' }
    ],

    sidebar: [
      {
        text: 'Introduction',
        items: [
          { text: 'What is Mora?', link: '/what-is-mora' },
          { text: 'Getting Started', link: '/getting-started' },
          { text: 'Available Commands', link: '/available-commands' }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/senkulabs/mora' }
    ]
  }
})
