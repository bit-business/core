import resource from "../resource";
import QueryString from "../query-string";


const apiPrefix = process.env.MIX_API_ROUTE_PREFIX
  ? "/" + process.env.MIX_API_ROUTE_PREFIX
  : "/skijasi-api";

export default {


  hidenotification(messageId, data) {
    return resource.put(apiPrefix + `/v1/poruke/hidenotification/${messageId}`, data);
  },

  markread(messageId, data) {
    return resource.put(apiPrefix + `/v1/poruke/markread/${messageId}`, data);
  },
  
  
  

  markallread(messageId, data) {
    return resource.put(apiPrefix + `/v1/poruke/markallread/${messageId}`, data);
  },
  

  getMessages() {
    const url = apiPrefix + "/v1/poruke/poruke";
    return resource.get(url);
  },

  getMyMessages() {
    const url = apiPrefix + "/v1/poruke/pregledporuke";
    return resource.get(url);
  },

  deleteMessage(messageId) {
    return resource.delete(apiPrefix + `/v1/poruke/${messageId}`);
  },

  updateMessage(messageId, data) {
    return resource.put(apiPrefix + `/v1/poruke/${messageId}`, data);
  },
  sendMessage(data) {
    return resource.post(apiPrefix + "/v1/poruke", data);
  },





  saveTokenMessage(firebaseTokenMessages) {
    const url = `${apiPrefix}/v1/firebase/cloud_messages/save-token-messages`;
    return resource.put(url, {
      token_get_message: firebaseTokenMessages,
    });
  },

  readMessage(id) {
    const url = `${apiPrefix}/v1/firebase/messages/${id}`;
    return resource.put(url);
  },
  getCountUnreadMessage() {
    const url = `${apiPrefix}/v1/firebase/messages/count-unread`;
    return resource.get(url);
  },


  sendFirebaseMessage() {
    const url = `${apiPrefix}/v1/firebase/cloud_messages/send-firebase-message`;
    return resource.post(url);
  }
  
};
