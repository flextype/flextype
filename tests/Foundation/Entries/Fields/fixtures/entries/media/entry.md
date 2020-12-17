---
title: Media
media:
  folders:
    fetch:
      macroable_folder:
        id: 'foo'
        method: fetchExtraData
        options: []
      foo_folder:
        id: 'foo'
      collection_of_folders:
        id: '/'
        options:
          collection: true
  files:
    fetch:
      macroable_file:
        id: 'foo'
        method: fetchExtraData
        options: []
      foo_file:
        id: foo.txt
      collection_of_files:
        id: '/'
        options:
          collection: true
---
