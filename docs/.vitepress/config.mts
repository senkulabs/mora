import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Mora by SenkuLabs",
  description: "Modular Laravel with native artisan conventions.",
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
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/senkulabs/mora' }
    ]
  }
})
