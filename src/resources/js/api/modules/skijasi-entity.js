import resource from "../resource";
import QueryString from "../query-string";

const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

const entityPrefix = apiPrefix + "/v1/entities";

export default {
  browse(data = {}) {
    const ep = entityPrefix + "/" + data.slug;
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  sendNewHzutsNotification(slug, userId) {
    const ep = `${entityPrefix}/${slug}/send-hzuts-notification/${userId}`;
    return resource.post(ep);
  },

  generatepdff(data) {
    const ep = entityPrefix + "/" + data.slug + "/generatepdff";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url, { responseType: 'blob' }); // Add the responseType option
  },
  generatepdffprint(data) {
    const ep = entityPrefix + "/" + data.slug + "/generatepdffprint";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url, { responseType: 'blob' }); // Add the responseType option
  },
  generatepdffprintcopy(data) {
    const ep = entityPrefix + "/" + data.slug + "/generatepdffprintcopy";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url, { responseType: 'blob' }); // Add the responseType option
  },

    generatepdffid(data) {
    const ep = entityPrefix + "/" + data.slug + "/generatepdffid";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url, { responseType: 'blob' }); // Add the responseType option
  },

  generatepdffpotvrdaisia(data) {
    const ep = entityPrefix + "/" + data.slug + "/generatepdffpotvrdaisia";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url, { responseType: 'blob' }); // Add the responseType option
  },

  generatepdffpotvrdaivsi(data) {
    const ep = entityPrefix + "/" + data.slug + "/generatepdffpotvrdaivsi";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url, { responseType: 'blob' }); // Add the responseType option
  },
  
  all(data = {}) {
    const ep = entityPrefix + "/" + data.slug + "/all";
    const url = ep;
    return resource.get(url);
  },

  read(data) {
    const ep = entityPrefix + "/" + data.slug + "/read";
    const qs = QueryString(data);
    const url = ep + qs;
    return resource.get(url);
  },

  citanje(params = {}) {

    const ep = entityPrefix + "/" + "hzuts-clanovi" + "/citanje";
    const qs = QueryString(params);
    const url = ep + qs;
    return resource.get(url);
  },

  edit(data) {
    return resource.put(entityPrefix + "/" + data.slug + "/edit", {
      data: data.data,
    });
  },

  editDatoteke(data) {
    return resource.put(entityPrefix + "/" + "hzuts-clanovi"  + "/editDatoteke", {
      data: data.data,
    });
  },


  add(data) {
    return resource.post(entityPrefix + "/" + data.slug + "/add", {
      data: data.data,
    });
  },

  restore(data) {
    const paramData = {
      data: data,
    };
    return resource.delete(
      entityPrefix + "/" + data.slug + "/restore",
      paramData
    );
  },

  delete(data) {
    const paramData = {
      data: data,
    };
    return resource.delete(
      entityPrefix + "/" + data.slug + "/delete",
      paramData
    );
  },


  deleteByFormId(data) {
    const paramData = {
      data: data,
    };
    return resource.delete(
      entityPrefix + "/" + data.slug + "/deleteByFormId",
      paramData
    );
  },



  deleteMultiple(data) {
    const paramData = {
      data: data,
    };
    return resource.delete(
      entityPrefix + "/" + data.slug + "/delete-multiple",
      paramData
    );
  },

  restoreMultiple(data) {
    const paramData = {
      data: data,
    };
    return resource.delete(
      entityPrefix + "/" + data.slug + "/restore-multiple",
      paramData
    );
  },

  sort(data) {
    return resource.put(entityPrefix + "/" + data.slug + "/sort", data);
  },
  maintenance(data = {}) {
    return resource.post(entityPrefix + "/" + data.slug + "/maintenance", data);
  },
};
