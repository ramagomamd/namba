var app = new Vue({
	el: '#app',
	data: {
      albums: {
        main: true,
        list: true
      },
      singles: {
        main: false,
        list: true
      }
    },

    methods: {
        toggleMain(tab) {
          this.albums.main = (tab == "albums" ? true : false)
          this.singles.main = (tab == "singles" ? true : false)
        },
        toggleAlbum(tab) {
          this.albums.list = (tab == "list" ? true : false)
        },
        toggleSingle(tab) {
          this.singles.list = (tab == "list" ? true : false)
        }
	}
})