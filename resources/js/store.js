import Vuex from 'vuex';

export default new Vuex.Store({
    state: {
        user: {},
        users: [],
    },
    mutations: {
        SET_USERS: (state, data) => state.users = data
    },
    actions: {
        async getUsers() {
            await axios.get("/api/users").then((response) => {
                this.commit('SET_USERS', response.data);
            });
        },
    }
});
