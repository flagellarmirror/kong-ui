<template>
    <div>

        <oauth-modal
            v-if="oauthModal"
            @close-modal="closeOauthModal($event)"
            :item="select"
        ></oauth-modal>

        <oauth-table
            ref="oauths"
            @event="openOauthModal($event)"
        ></oauth-table>

    </div>
</template>

<script>
module.exports={
    data:function(){
        return{
            oauthModal: false,
            select:null,
        }
    },
    methods: {
        openOauthModal:function(id){
            this.select=id
            this.oauthModal=true
        },
        closeOauthModal:function(event){
            this.oauthModal=false
            this.select=null
            if(event) this.$refs.oauths.getOauths()
        },
    },
    created:function() {
    },
    components:{
        'oauth-modal': httpVueLoader('./oauthModal.vue' + '?v=' + new Date().getTime()),
        'oauth-table': httpVueLoader('./oauthTable.vue' + '?v=' + new Date().getTime())
    }
}
</script>
