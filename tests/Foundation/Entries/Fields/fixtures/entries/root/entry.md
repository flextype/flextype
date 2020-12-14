---
title: Root
fetch:
  single:
    id: albums
    options:
      from: single
  collection:
    id: albums
    options:
      from: collection
  collectionWithDepth:
    id: albums
    options:
      from: collection
      find:
        depth: '>0'
---
