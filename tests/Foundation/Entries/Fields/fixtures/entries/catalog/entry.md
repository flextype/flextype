---
title: Catalog
visibility: visible
entries:
  fetchSingle:
    label1:
      id: discounts/50-off
      options:
        filter:
          limit: 4
  fetchCollection:
    bikes:
      id: catalog/bikes
      options:
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
