// SelectSearch Component
const SelectSearch = {
    template: `
      <div class="select-search">
        <span class="d-flex justify-content-between form-control" @click="containerClick"><span>{{s_option.name}}</span><span><i :class="{ 'icon-chevron-down': !showList, 'icon-chevron-up': showList }"></i></span></span>
        <div class="px-2 pt-2" v-show="showList" ref="listDiv">
            <input type="text" v-model="search" class="form-control mb-1" @input="filterOptions">
            <ul v-if="filteredOptions.length">
                <li class="li-opt" v-for="option in filteredOptions" :key="option.id" @click="selectOption(option)">
                    {{ option.name }}
                </li>
            </ul>
            <ul v-else>
                <li class="li-st">
                    No Result
                </li>
            </ul>
        </div>
      </div>
    `,
    props: ["options"],
    data() {
        return {
            search: "",
            s_option: this.options[0],
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
                option.name.toLowerCase().includes(searchLower)
            );

            this.filteredOptions = filterOptions;
        },
        selectOption(option) {
            this.showList = false;
            this.s_option = option;
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
            this.$emit("clickx", this);
        },
        hideList() {
            this.showList = false;
        },
    },
};
