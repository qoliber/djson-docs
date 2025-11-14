import { defineConfig, loadEnv } from 'vitepress'
import { fileURLToPath } from 'node:url'

export default defineConfig(({ mode }) => {
  // Load environment variables
  const env = loadEnv(mode, fileURLToPath(new URL('../../', import.meta.url)))
  const gtmId = env.VITE_GTM_ID

  return {
  title: 'DJson',
  description: 'Dynamic JSON templating library for PHP',

  head: gtmId ? [
    // Google Tag Manager - Head Script
    ['script', {}, `(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','${gtmId}');`]
  ] : [],

  // Google Tag Manager - Body noscript (proper GTM implementation)
  transformHtml: gtmId ? (code) => {
    const noscript = `<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=${gtmId}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>`
    return code.replace(/<body>/, `<body>\n${noscript}`)
  } : undefined,

  markdown: {
    theme: 'material-theme-palenight',
    lineNumbers: true
  },

  themeConfig: {
    logo: '/logo.svg',

    nav: [
      { text: 'Guide', link: '/guide/getting-started' },
      { text: 'Reference', link: '/api/directives' },
      { text: 'Examples', link: '/examples/ecommerce' },
      { text: 'About', link: '/about' },
      {
        text: 'v1.0.0',
        items: [
          { text: 'Changelog', link: '/changelog' },
          { text: 'Contributing', link: '/contributing' }
        ]
      }
    ],

    sidebar: {
      '/guide/': [
        {
          text: 'Introduction',
          items: [
            { text: 'What is DJson?', link: '/guide/what-is-djson' },
            { text: 'Getting Started', link: '/guide/getting-started' },
            { text: 'Why DJson?', link: '/guide/why-djson' }
          ]
        },
        {
          text: 'Basics',
          items: [
            { text: 'Variables', link: '/guide/variables' },
            { text: 'Loops', link: '/guide/loops' },
            { text: 'Conditionals', link: '/guide/conditionals' },
            { text: 'Functions', link: '/guide/functions' }
          ]
        },
        {
          text: 'Advanced',
          items: [
            { text: 'Match/Switch', link: '/guide/match-switch' },
            { text: 'Computed Values', link: '/guide/computed-values' },
            { text: 'Ternary Operators', link: '/guide/ternary' },
            { text: 'Logical Operators', link: '/guide/logical-operators' },
            { text: 'Template Validation', link: '/guide/validation' }
          ]
        },
        {
          text: 'Extending',
          items: [
            { text: 'Custom Directives', link: '/guide/custom-directives' },
            { text: 'Custom Functions', link: '/guide/custom-functions' }
          ]
        }
      ],
      '/api/': [
        {
          text: 'Reference',
          items: [
            { text: 'Directives', link: '/api/directives' },
            { text: 'Functions', link: '/api/functions' }
          ]
        }
      ],
      '/examples/': [
        {
          text: 'Examples',
          items: [
            { text: 'E-commerce Catalog', link: '/examples/ecommerce' },
            { text: 'API Responses', link: '/examples/api-responses' },
            { text: 'Schema.org JSON-LD', link: '/examples/schema-org' }
          ]
        }
      ]
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/qoliber/djson' }
    ],

    search: {
      provider: 'local'
    },

    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2025 Qoliber'
    },

    editLink: {
      pattern: 'https://github.com/qoliber/djson-docs/edit/main/:path',
      text: 'Edit this page on GitHub'
    }
  }
}})
