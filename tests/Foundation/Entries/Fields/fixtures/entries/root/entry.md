---
title: Root
fetch:
  single:
    from: single
    id: albums
  collection:
    from: collection
    id: albums
  collectionWithDepth:
    from: collection
    id: albums
    options:
      find:
        depth: '>0'
---
