<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Chat</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex"
          style="min-height: 400px; max-height: 400px"
        >
          <!-- Users -->
          <section
            class="
              w-3/12
              bg-gray-300 bg-opacity-30
              border-r border-gray-300
              overflow-y-scroll
            "
          >
            <ul>
              <li
                class="
                  p-6
                  text-lg text-gray-500
                  leading-7
                  font-semibold
                  border-b border-gray-200
                  hover:bg-gray-200 hover:bg-opacity-50
                  hover:cursor-pointer
                "
                v-for="(user, index) of users"
                :key="index"
                @click="loadMessagesFromUser(user.id)"
                :class="
                  userSelected && userSelected == user.id ? 'bg-gray-200' : ''
                "
              >
                <div class="flex items-center">
                  {{ user.name }}
                  <span class="ml-auto w-7 h-7 rounded-full bg-blue-900"></span>
                </div>
              </li>
            </ul>
          </section>

          <!-- Message -->
          <section
            class="
              w-9/12
              bg-gray-200 bg-opacity-30
              flex flex-col
              justify-between
            "
          >
            <div class="w-full p-6 flex flex-col overflow-y-auto">
              <div
                class="w-full mb-3 message"
                :class="
                  message.from_user_id === this.$inertia.page.props.auth.user.id
                    ? ' text-right'
                    : ''
                "
                v-for="(message, index) of messages"
                :key="index"
              >
                <p
                  :class="
                    message.from_user_id ===
                    this.$inertia.page.props.auth.user.id
                      ? 'messageFrom'
                      : 'messageTo'
                  "
                  class="inline-block rounded-md p-2"
                  style="max-width: 75%"
                >
                  {{ message.content }}
                </p>
                <span class="block mt-1 text-xs text-gray-400">
                  {{ message.created_at }}
                </span>
              </div>
            </div>

            <section
              v-show="userSelected"
              class="
                w-full
                bg-gray-200 bg-opacity-25
                p-6
                border-t border-gray-200
              "
            >
              <form @submit.prevent="storeMessage">
                <div
                  class="flex rounded-md overflow-hidden border border-gray-300"
                >
                  <input
                    autofocus="autofocus"
                    v-model="form.content"
                    type="text"
                    class="flex-1 px-4 py-2 text-sm focus:outline-none border-0"
                  />
                  <button
                    type="submit"
                    class="
                      bg-indigo-700
                      hover:bg-indigo-900
                      text-white
                      px-4
                      py-2
                    "
                  >
                    Enviar
                  </button>
                </div>
              </form>
            </section>
          </section>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import { Vue } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import { mapState, mapActions } from "vuex";
export default {
  components: {
    AppLayout,
  },
  mounted() {
    this.getUsers();
    console.log(this.userSelected);
    Echo.private(`user.${this.$inertia.page.props.auth.user.id}`).listen(
      ".MessageCreated",
      (e) => {
        if (this.userSelected && this.userSelected === e.model.from_user_id) {
          this.messages.push(e.model);
          this.scrollToBottom();
        } else {
          const user = this.users.filter((user) => {
            if (user.id === e.model.from_user_id) return user;
          });

          if (user) {
            Vue.set(user[0], "notification", true);
          }
        }
      }
    );
  },
  data() {
    return {
      userSelected: null,
      messages: [],
      form: {
        content: null,
      },
    };
  },
  computed: {
    ...mapState(["users"]),
  },
  methods: {
    ...mapActions(["getUsers"]),
    scrollToBottom() {
      if (this.messages.length) {
        document.querySelectorAll(".message:last-child")[0].scrollIntoView();
      }
    },
    async loadMessagesFromUser(userId) {
      this.userSelected = userId;
      await axios.get(`/api/messages/${userId}`).then((response) => {
        this.messages = response.data;
      });
      this.scrollToBottom();
    },
    async storeMessage() {
      await axios
        .post("api/messages", {
          content: this.form.content,
          to_user_id: this.userSelected,
        })
        .then((response) => {
          this.messages.push(response.data);
          this.form.content = null;
        });

      this.scrollToBottom();
    },
  },
};
</script>