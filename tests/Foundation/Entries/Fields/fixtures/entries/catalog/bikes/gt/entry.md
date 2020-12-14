---
title: GT
brand: gt
entries:
  fetchCollection:
    discounts_available:
      id: discounts
      options:
        filter:
          where:
            -
              key: category
              operator: eq
              value: bikes
  fetchSingle:
    label1:
      id: discounts/50-off
      options:
        filter:
          limit: 3
    label2:
      id: discounts/30-off
      options:
        filter:
          limit: 2
---
