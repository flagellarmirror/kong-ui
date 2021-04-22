<template>
    <v-data-table
        :headers="headers"
        :items="oauths_filtered"
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
                            clearable
                            @input="filter"
                            v-model="head.custom.value"
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
            oauths:[],
            oauths_filtered:[],
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
                    text: 'Oauths ID',
                    align: 'start',
                    value: 'id',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
                {
                    text: 'Name',
                    value: 'name',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
                {
                    text: 'Client id',
                    value: 'client_id',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
                {
                    text: 'Consumer ID',
                    value: 'consumer_id',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
                {
                    text: 'Created at',
                    value: 'custom_data',
                    custom:{
                        type:"text",
                        value:null,
                    }
                },
            ],
        }
    },
    methods: {
        sendEvent:function(index){
            this.$emit('event',this.oauths_filtered[index].id)
        },
        filter:function(){
            this.oauths_filtered=JSON.parse(JSON.stringify(this.oauths))
            for(var i=this.oauths_filtered.length-1;i>=0;i--){
                for(var k=1;k<this.headers.length;k++){
                    if(this.oauths_filtered[i][this.headers[k].value]==null) {
                        if(this.headers[k].custom.value!=null&&this.headers[k].custom.value!=''){
                            this.oauths_filtered.splice(i,1)
                            break
                        }
                    }else{
                        if(this.headers[k].custom.value==null) continue
                        if(!this.oauths_filtered[i][this.headers[k].value].toLowerCase().includes(this.headers[k].custom.value.toLowerCase())){
                            this.oauths_filtered.splice(i,1)
                            break
                        }
                    }
                }
            }
        },
        deleteRow:function(index){
            var self=this
            Swal.fire({
                title: 'Delete oauth',
                text: "Are you sure you want to delete oauth?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f86c6b',
                cancelButtonColor: '#20a8d8',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Back'
            }).then( function (result) {
                if(result.value){
                    var params={
                        id: self.oauths_filtered[index].id
                    }
                    Utils.apiCall("delete", "/kong/oauth",params)
                    .then(function (response) {
                        if(response!=undefined){
                            Swal.fire({
                                type: 'success',
                                title: 'Oauth delete',
                                text: 'Oauth delete',
                            }).then(function(result) {
                                self.getOauths()
                            })
                        }
                    });
                }
            })
        },
        getOauths:function(){
            var self=this
            Utils.apiCall("get", "/kong/oauth")
            .then(function (response) {
                console.log(response)
                var tmp=[]
                for(var i=0;i<response.data.data.length;i++){
                    if(response.data.data[i].consumer){
                        response.data.data[i].consumer_id=response.data.data[i].consumer.id
                    }
                    response.data.data[i].custom_data=moment.unix(response.data.data[i].created_at).format("MM-DD-YYYY HH:mm:ss")
                    tmp.push(response.data.data[i])
                }
                self.oauths=tmp
                self.oauths_filtered=self.oauths
                self.filter()
            });
        }
    },
    created:function() {
        this.getOauths();
    },
}
</script>
