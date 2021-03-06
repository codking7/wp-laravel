var blanks = require('./brief-data-blanks');

export default Vue.extend({
    props: {
        checklist: {
            type: Array,
            required: true
        },
        path: {
            type: String,
            required: true
        },
        withCategories: {
            type: Boolean,
            required: false,
            default: false
        }
    },
    data() {
        return {
            blanks: blanks
        }
    },
    components: {
        'uploadcare': require('./brief-uploadcare')
    },
    template: `
        <div class="form-group">
            <label>Check List Items</label>

            <div class="well well-small text-center" v-show=" !checklist.length ">
                <h4 class="m-a-0">There are no check list items added yet.</h4>
                <br />
                <a href="#" class="btn btn-xs btn-success"
               v-on:click.prevent="$root.addListItem(path)"><i class="fa fa-plus"></i> Add Item</a>
            </div>
            <table class="table table-bordered table-middle"
                   v-show="checklist.length">
                <thead>
                <tr>
                    <th v-if="withCategories" style="width: 20%">Category</th>
                    <th style="width: 50%">Description</th>
                    <th style="width: 20%">Screenshots</th>
                    <th style="width: 10%">&nbsp;</th>
                </tr>
                </thead>

                <tbody>


                <tr v-for="(itemIndex, item) in checklist">
                    <td v-if="withCategories">
                        <select v-model="item.category" class="custom-select form-control">
                            <option v-for="option in blanks.select.checklist_item_categories" value="{{ option.value }}">
                                {{ option.text }}
                            </option>
                        </select>
                    </td>
                    <td>
                        <textarea class="form-control m-a-0" rows="5" placeholder="Enter a description of the item."
                                  v-model="item.description"></textarea>
                    </td>
                    <td>
                        <uploadcare
                            :bind-files.sync="item.screenshots"
                            :all-files.sync="$root.briefFiles"
                            reference_type="project_brief"
                            reference_id="$root.state.brief_id">
                        </uploadcare>
                    </td>

                    <td>
                        <a href="#" class="btn btn-danger-outline"
                           v-on:click.prevent="$root.removeListItem(path, itemIndex)"><i class="fa fa-times"></i></a>
                    </td>
                </tr>

                </tbody>
            </table>

            <a v-show="checklist.length" href="#" class="btn btn-xs btn-success pull-right"
               v-on:click.prevent="$root.addListItem(path)"><i class="fa fa-plus"></i> Add Item</a>
        </div>
    `
});