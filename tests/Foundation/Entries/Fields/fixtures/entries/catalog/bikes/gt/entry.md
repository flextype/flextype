---
title: GT
brand: gt
fetch:
  discounts_available:
    id: discounts
    from: collection
    options:
      filter:
        where:
        -
          key: category
          operator: eq
          value: bikes
  label1:
    from: single
    id: discounts/50-off
    options:
      filter:
        limit: 3
  label2:
    from: single
    id: discounts/30-off
    options:
      filter:
        limit: 2
---
