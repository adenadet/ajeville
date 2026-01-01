<template>
    <section>
        <li class="nav-item dropdown">
            <a id="dropdownSubMenu2" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
                <span class="profile-text font-weight-normal"><i class="mr-1 fas fa-hospital"></i> {{ current_branch.name }}</span>
            </a>
            <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                <a class="dropdown-item" v-for="branch in branches" @click="updateBranch(branch)"><li><i class="mr-1 fas fa-hospital"></i>{{ branch.name }}</li></a>
            </ul>
        </li>

    </section>
</template>
<script>
export default {
    computed:{
        current_branch(){
            var branch = this.$store.getters.currentBranch;
            if (branch == null){this.updateBranch(this.staff_branch); branch = this.$store.getters.currentBranch;}
            return branch;
        },
    },
    data(){
        return  {
            editMode: false,
        }
    },
    emits:['changeLocation'],
    mounted() {
        this.$store.dispatch('getBranchCookie');
    },
    methods:{
        updateBranch(branch){
            this.$store.dispatch('setBranchCookie', branch);
            this.$emit('changeLocation');
        }
    },
    props: {
        branches: Array,
        staff_branch: Object,
    }
}
</script>