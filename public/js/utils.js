MyApp = {
    copy_vals(source, target, except_list = []) {
        for (let k in target) {
            if (except_list.includes(k)) continue;
            target[k] = source[k];
        }
    },
};
