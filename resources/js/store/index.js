import { createStore } from 'vuex';

const store = createStore({
    state:{
        branch: {},
        user : {},
        roles: [],
        patient: {},
        visit: {},
    },
    getters: {
        branch_modules(state) {
            return state.branch.modules;
        },
        currentBranch(state){
            return state.branch;
        },
        currentPatient(state){
            if (state.patient == null){
                this.getPatientCookie(context);
            }
            return state.patient;
        },
        currentVisit(state){
            return state.visit;
        },
        hasRole: (state) => (role) => {
            return state.roles.includes(role);
        },
        hasAnyRole: (state) => (roles) => {
            return roles.some(r => state.roles.includes(r));
        },
    },
    mutations: {
        clearUser(state){
            state.user = null;
            state.roles = [];
        },
        setUser(state, user){
            state.user = user;
            state.roles = user.roles || [];
        },
        updateBranch(state, branch){
            state.branch = branch;
        },
        updatePatient(state, patient){
            state.patient = patient;
        },
        updateVisit(state, visit){
            state.visit = visit;
        },
    },
    actions: {
        fetchUser({ commit }) {
        return axios.get('/api/ums/users/auth')
            .then(response => {
            commit('setUser', response.data);
            });
        },
        getBranchCookie(context){
            if (context.branch == null || context.branch.id == null){
                axios.get('/api/operations/branches/get_cookie')
                .then((response)=> {
                    var branch = response.data.branch;
                    if (branch == null){branch = response.data.user.branch;}
                    context.commit('updateBranch', branch)
                    return context.branch;
                })    
            }
        },
        getPatientCookie(context){
            if (context.patient == null || context.patient.id == null){
                axios.get('/api/emr/hims/patients/get_cookie')
                .then((response)=> {
                    var patient = response.data.patient;
                    context.commit('updatePatient', patient)
                })    
            }
        },
        setBranchCookie(context, branch){
            context.commit('updateBranch', branch);
            axios.post('/api/operations/branches/set_cookie', { 'branch' : branch})
            .then(()=> {location.reload();})
        },
        setPatient(context, patient){
            context.commit("updatePatient", patient);
        },
        setPatientCookie(context, patient){
            context.commit('updatePatient', patient);
            //this.$emit('patientReset');
            axios.post('/api/emr/hims/patients/set_cookie', {'patient' : patient})
            .then(()=>{})
        },
        setVisitCookie(context, visit){
            context.commit('updateVisit', visit);
            axios.post('/api/emr/hims/visits/set_cookie', {'visit' : visit})
        },
    }
}); 

export default store;