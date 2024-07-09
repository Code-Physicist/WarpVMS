MyApp = {
    role_type_dict: {1:"Operator", 2:"Supervisor", 3:"Sup & Opt"},
    opn_type_badge_dict: {1:"<span class='badge border border-success bg-success-subtle text-success'>Data</span>", 2:"<span class='badge border border-warning bg-warning-subtle text-warning'>Service</span>", 3:"<span class='badge border border-info bg-info-subtle text-info'>Manufacturing</span>"},
    opn_type_dict: {1:"Data", 2:"Service", 3:"Manufacturing"},
    copy_vals: function(source, target, except_list=[]){
        for(let k in target) {
            if(except_list.includes(k)) continue;
            target[k] = source[k];
        }
    },
    async get_provinces() {
        const res = await axios.get("/api/get_provinces");
        return res.data.data_list;
    },
    async get_amphurs(province_id) {
        const res = await axios.get(`/api/get_amphurs?province_id=${province_id}`);
        return res.data.data_list;
    },
    async get_tambons(amphur_id) {
        const res = await axios.get(`/api/get_tambons?amphur_id=${amphur_id}`);
        return res.data.data_list;
    },
    async get_sites() {
        const res = await axios.get("/api/get_sites");
        return res.data.data_list;
    },
    async get_operations() {
        const res = await axios.get("/api/get_operations");
        return res.data.data_list;
    },
}