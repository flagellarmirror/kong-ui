<template>
    <v-data-table
        :headers="headers"
        :items="tokens"
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
                        <v-btn
                            icon
                            @click="deleteRow(index)"
                        >
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                        <!-- <v-btn
                            icon
                            @click="sendEvent(index)"
                        >
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn> -->
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
            tokens:[],
            headers: [
                {
                    text: 'Actions',
                    value: null,
                    align: 'center',
                    sortable: false,
                    custom:{
                        type:null
                    }
                },
                {
                    text: 'Token ID',
                    align: 'start',
                    value: 'id',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Authenticated user',
                    value: 'authenticated_userid',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Scopes',
                    value: 'scope',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Credential id',
                    value: 'credential_id',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Service id',
                    value: 'service_id',
                    custom:{
                        type:"text"
                    }
                },
                {
                    text: 'Created at',
                    value: 'custom_data',
                    custom:{
                        type:"text"
                    }
                },
            ],
        }
    },
    methods: {
        sendEvent:function(index){
            this.$emit('event',this.tokens[index].id)
        },
        deleteRow:function(index){
            var self=this
            var params={
                id: this.tokens[index].id
            }
            Utils.apiCall("delete", "/kong/tokens",params)
            .then(function (response) {
                if(response!=undefined){
                    Swal.fire({
                        type: 'success',
                        title: 'Token delete',
                        text: 'Token delete',
                    }).then(function(result) {
                        self.getTokens()
                    })
                }
            });
        },
        getTokens:function(){
            var self=this
            Utils.apiCall("get", "/kong/tokens")
            .then(function (response) {
                console.log(response)
                var tmp=[]
                for(var i=0;i<response.data.data.length;i++){
                    if(response.data.data[i].credential!=null){
                        response.data.data[i].credential_id=response.data.data[i].credential.id
                    }
                    if(response.data.data[i].service!=null){
                        response.data.data[i].service_id=response.data.data[i].service.id
                    }
                    response.data.data[i].custom_data=moment.unix(response.data.data[i].created_at).format("MM-DD-YYYY HH:mm:ss")
                    tmp.push(response.data.data[i])
                }
                self.tokens=tmp
            });
        }
    },
    created:function() {
        this.getTokens();
    },
}
</script>
