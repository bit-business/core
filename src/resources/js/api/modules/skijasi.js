/* eslint-disable no-unused-vars */
import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {

  getFormEntries() {
    return resource.get(`${apiPrefix}/v1/form-entries`);
  },

  getFormEntriesByHotel(hotel) {
    return resource.get(`${apiPrefix}/v1/form-entries/by-hotel`, {
      params: { hotel }
    });
  },

  getFormEntriesByFormId(formId) {
    return resource.get(`${apiPrefix}/v1/form-entries/by-form`, {
      params: { form_id: formId }
    });
  }
};