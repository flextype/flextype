---
title: Catalog
visibility: draft
fetch:
  label1:
    id: discounts/50-off
    options:
      from: single
      filter:
        limit: 4
  bikes:
    id: catalog/bikes
    options:
      from: collection
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
      from: collection
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
