<template>
    <div>

        <!-- <oauth-modal
            v-if="oauthModal"
            @close-modal="closeOauthModal($event)"
            :item="select"
        ></oauth-modal> -->

        <tokens-table
            ref="tokens"
            @event="openOauthModal($event)"
        ></tokens-table>

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
            if(event) this.$refs.tokens.getTokens()
        },
    },
    created:function() {
    },
    components:{
        // 'oauth-modal': httpVueLoader('./oauthModal.vue' + '?v=' + new Date().getTime()),
        'tokens-table': httpVueLoader('./tokensTable.vue' + '?v=' + new Date().getTime())
    }
}
</script>
