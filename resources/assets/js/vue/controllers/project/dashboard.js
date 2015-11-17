export default Vue.extend({

    props: {
        state: {
            type: String,
            required: true
        }
    },

    data() {
        return {
            loaded: false,
            posting: false,
            data: [],
            message: ''
        }
    },

    attached() {
        this.state = JSON.parse(this.state);
    },

    ready() {
        this.getThreads();
    },

    methods: {
        getThreads() {
            this.$http.get(`/api/threads`, this.state, (data) => {
                this.data = data.data;
                this.loaded = true;
            });
        },
        postMessage(e) {
            this.posting = true;

            this.$http.post(`/api/threads`, _.extend(this.state, {content: this.message}), (data) => {
                this.message = '';
                this.data.unshift(data.data);
            }).always(() => {
                this.posting = false;
            });
        }
    }

});