<template>
    <div>

        <consumer-modal
            v-if="consumerModal"
            @close-modal="closeConsumerModal($event)"
            :item="select"
        ></consumer-modal>

        <consumers-table
            ref="consumers"
            @event="openConsumerModal($event)"
        ></consumers-table>

    </div>
</template>

<script>
module.exports={
    data:function(){
        return{
            consumerModal: false,
            select:null,
        }
    },
    methods: {
        openConsumerModal:function(id){
            this.select=id
            this.consumerModal=true
        },
        closeConsumerModal:function(event){
            this.consumerModal=false
            this.select=null
            if(event) this.$refs.consumers.getConsumers()
        },
    },
    created:function() {
    },
    components:{
        'consumer-modal': httpVueLoader('./consumerModal.vue' + '?v=' + new Date().getTime()),
        'consumers-table': httpVueLoader('./consumersTable.vue' + '?v=' + new Date().getTime())
    }
}
</script>
