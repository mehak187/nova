Nova.booting((Vue, router, store) => {
  Vue.component(
    'index-camera-upload',
    require('./components/IndexField').default
  )
  Vue.component(
    'detail-camera-upload',
    require('./components/DetailField').default
  )
  Vue.component(
    'form-camera-upload',
    require('./components/FormField').default
  )
})
