---
title: Catalog
visibility: visible
entries:
  fetch:
    label1:
      id: discounts/50-off
      options:
        filter:
          limit: 4
    bikes:
      id: catalog/bikes
      options:
        collection: true
        filter:
          where:
            -
              key: brand
              operator: eq
              value: gt
          limit: 10
    discounts:
      id: discounts
      options:
        collection: true
        filter:
          where:
            -
              key: title
              operator: eq
              value: '30% off'
            -
              key: category
              operator: eq
              value: bikes
---
