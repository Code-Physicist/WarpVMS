// SelectSearch Component
const SelectSearch = {
    template: `
      <div class="select-search">
        <span class="d-flex justify-content-between form-control" @click="containerClick"><span>{{value}}</span><span><i :class="{ 'icon-chevron-down': !showList, 'icon-chevron-up': showList }"></i></span></span>
        <div class="px-2 pt-2" v-show="showList" ref="listDiv">
            <input type="text" v-model="search" class="form-control mb-1" @input="filterOptions">
            <ul v-if="filteredOptions.length">
                <li v-for="option in filteredOptions" :key="option" @click="selectOption(option)">
                    {{ option }}
                </li>
            </ul>
        </div>
      </div>
    `,
    props: ["options"],
    data() {
        return {
            search: "",
            value: "",
            filteredOptions: this.options,
            showList: false,
        };
    },
    watch: {
        options(newOptions) {
            this.filteredOptions = newOptions;
        },
    },
    methods: {
        filterOptions() {
            const searchLower = this.search.toLowerCase();
            let filterOptions = this.options.filter((option) =>
                option.toLowerCase().includes(searchLower)
            );

            if (filterOptions.length > 0) this.filteredOptions = filterOptions;
            else this.filteredOptions = ["No results"];
        },
        selectOption(option) {
            this.showList = false;
            this.value = option;
            //this.filteredOptions = [];
            this.$emit("select", option);
        },
        containerClick() {
            if (this.showList) {
                this.showList = false;
            } else {
                this.search = "";
                this.filterOptions();
                this.showList = true;
                this.$nextTick(() => {
                    this.$refs.listDiv.scrollTop = 0;
                });
            }
        },
    },
};
