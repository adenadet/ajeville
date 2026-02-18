import { createStore } from 'vuex';
import axios from 'axios';

const store = createStore({
    state: {
        branch: null,
        user: null,
        roles: [],
        patient: null,
        visit: null,
    },
    getters: {
        branch_modules: state => state.branch?.modules || [],

        currentBranch: state => state.branch,
        currentPatient: state => state.patient,
        currentVisit: state => state.visit,
        
        hasRole: state => role => state.roles.includes(role),
        hasAnyRole: state => roles => roles.some(r => state.roles.includes(r)),
    },
    mutations: {
        clearUser(state) {state.user = null; state.roles = [];},
        setUser(state, user) {state.user = user; state.roles = user?.roles || [];},
        updateBranch(state, branch) {state.branch = branch;},
        updatePatient(state, patient) {state.patient = patient;},
        updateVisit(state, visit) {state.visit = visit;},
        clearVisitContext(state) {state.patient = null; state.visit = null;},
    },

    actions: {
        async fetchUser({ commit }) {
            const response = await axios.get('/api/ums/users/auth');
            commit('setUser', response.data);
        },

        async hydrateBranch({ commit, state }) {
            if (state.branch?.id) return state.branch;

            const response = await axios.get('/api/operations/branches/get_cookie');
            const branch = response.data.branch || response.data.user.branch;

            commit('updateBranch', branch);
            return branch;
        },

        async getBranchCookie(context){
            if (context.branch == null || context.branch.id == null){
                await axios.get('/api/operations/branches/get_cookie')
                .then((response)=> {
                    var branch = response.data.branch;
                    if (branch == null){branch = response.data.user.branch;}
                    context.commit('updateBranch', branch)
                    return context.branch;
                })    
            }
        },

        async setBranchCookie({ commit }, branch) {
            commit('updateBranch', branch);
            await axios.post('/api/operations/branches/set_cookie', { branch });
        },

        async hydrateVisitContext({ commit }) {
            console.log("Working");
            const [patientRes, visitRes] = await Promise.all([
                axios.get('/api/emr/hims/patients/get_cookie'),
                axios.get('/api/emr/hims/visits/get_cookie'),
            ]);

            commit('updatePatient', patientRes.data.patient || null);
            commit('updateVisit', visitRes.data.visit || null);
        },

        setPatient({ commit }, patient) {
            commit('updatePatient', patient);
        },

        setPatientCookie({ commit }, patient) {
            commit('updatePatient', patient);
            return axios.post('/api/emr/hims/patients/set_cookie', { patient });
        },

        setVisitCookie({ commit }, visit) {
            commit('updateVisit', visit);
            return axios.post('/api/emr/hims/visits/set_cookie', { visit });
        },

        clearVisitContext({ commit }) {
            commit('clearVisitContext');
        },
    }
});

export default store;