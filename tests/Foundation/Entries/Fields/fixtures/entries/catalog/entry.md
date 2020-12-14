---
title: Catalog
visibility: draft
fetch:
  label1:
    from: single
    id: discounts/50-off
    options:
      filter:
        limit: 4
  bikes:
    id: catalog/bikes
    from: collection
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
    from: collection
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
