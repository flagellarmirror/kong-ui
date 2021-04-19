<template>
    <v-data-table
        :headers="headers"
        :items="consumers"
        :items-per-page="5"
        class="elevation-1"
        dense
    >
        <template v-slot:header="{ props }">
            <tr>
                <template v-for="(head,i) in props.headers">
                    <th :key="i">
                        <v-text-field
                            v-if="head.custom.type=='text'"
                            solo
                            dense
                            hide-details
                        ></v-text-field>
                        <v-btn
                            v-if="head.custom.type=='btn'"
                            icon
                            @click="$emit('event',null)"
                        >
                        <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </th>
                </template>
            </tr>
        </template>
        <template v-slot:item="{ item,index,headers }">
            <tr>
                <template v-for="(header,i) in headers">
                    <td :key="i" v-if="i==0" style="text-align:center">
                        <v-btn icon>
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                        <v-btn
                            icon
                            @click="sendEvent(index)"
                        >
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                    </td>
                    <td :key="i" v-else>{{item[header['value']]}}</td>
                </template>
            </tr>
        </template>
    </v-data-table>
</template>

<script>
module.exports = {
    data:function(){
        return{
            consumers:[],
            headers: [
                {
                    text: 'Actions',
                    value: null,
                    align: 'center',
                    sortable: false,
                    custom:{
                        type:"btn"
                    }
                },
                {
                    text: 'Consumer ID',
                    align: 'start',
                    value: 'id',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Username',
                    value: 'username',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Custom id',
                    value: 'custom_id',
                    custom:{
                        type:"text"
                    }
                },
            ],
        }
    },
    methods: {
        sendEvent:function(index){
            this.$emit('event',this.consumers[index].id)
        },
        getConsumers:function(){
            var self=this
            Utils.apiCall("get", "/kong/consumers")
            .then(function (response) {
                console.log(response)
                var tmp=[]
                for(var i=0;i<response.data.data.length;i++){
                    if(response.data.data[i].protocols!=null){
                        response.data.data[i].protocols=response.data.data[i].protocols.join(";")
                    }
                    tmp.push(response.data.data[i])
                }
                self.consumers=tmp
            });
        }
    },
    created:function() {
        this.getConsumers();
    },
}
</script>
