---
title: GT
brand: gt
fetch:
  discounts_available:
    id: discounts
    options:
      from: collection
      filter:
        where:
        -
          key: category
          operator: eq
          value: bikes
  label1:
    id: discounts/50-off
    options:
      from: single
      filter:
        limit: 3
  label2:
    id: discounts/30-off
    options:
      from: single
      filter:
        limit: 2
---
