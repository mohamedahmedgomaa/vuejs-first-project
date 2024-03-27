<script setup>
const props = defineProps({
  userData: {
    type: Object,
    required: false,
    default: () => ({
      id: 0,
      name: '',
      type: '',
      email: '',
      phone: '',
      is_active: '',
    }),
  },
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'refreshData',
  'update:isDialogVisible',
])

const type = ref('')
const name = ref('')
const phone = ref('')
const email = ref('')
const is_active = ref('')

const errors = ref({
  name: undefined,
  type: undefined,
  phone: undefined,
  email: undefined,
  is_active: undefined,
})

const refVForm = ref()

const updateUser = async () => {
  try {
    const res = await $api(`/users/crud/${ userData.value.id }`, {
      method: 'PATCH',
      body: {
        name: name.value,
        type: type.value,
        email: email.value,
        phone: phone.value,
        is_active: is_active.value,
      },
      onResponseError({ response }) {
        errors.value = response._data.errors
      },
    })

    emit('refreshData', res);
    // await nextTick(() => {
    //   router.replace(route.query.to ? String(route.query.to) : '/')
    // })
  } catch (err) {
    console.error(err)
  }
}

const userData = ref(structuredClone(toRaw(props.userData)))
const isUseAsBillingAddress = ref(false)

watch(props, () => {
  userData.value = structuredClone(toRaw(props.userData))
})

const fetchUser = () => {
  if(userData.value !== null){
    type.value = userData.value.type
    name.value = userData.value.name
    phone.value =  userData.value.phone
    email.value =  userData.value.email
    is_active.value =  userData.value.is_active
  }
}

onMounted(fetchUser)

const onFormSubmit = () => {
  emit('update:isDialogVisible', false)
  emit('submit', userData.value)

  refVForm.value?.validate().then(({ valid: isValid }) => {
    if (isValid)
      updateUser()
  })
}

const onFormReset = () => {
  userData.value = structuredClone(toRaw(props.userData))
  emit('update:isDialogVisible', false)
}

const dialogModelValueUpdate = val => {
  emit('update:isDialogVisible', val)
}
</script>

<template>
  <VDialog
    :width="$vuetify.display.smAndDown ? 'auto' : 900"
    :model-value="props.isDialogVisible"
    @update:model-value="dialogModelValueUpdate"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="dialogModelValueUpdate(false)" />

    <VCard class="pa-sm-10 pa-2">
      <VCardText>
        <!-- 👉 Title -->
        <h4 class="text-h4 text-center mb-2">
          Edit User Information
        </h4>
        <p class="text-body-1 text-center mb-6">
          Updating user details will receive a privacy audit.
        </p>

        <!-- 👉 Form -->
        <VForm
          ref="refVForm"
          class="mt-6"
          @submit.prevent="onFormSubmit"
        >
          <VRow>
            <!-- 👉 First Name -->
            <VCol
              cols="12"
              md="6"
            >{{name}}
              <AppTextField
                v-model="name"
                label="Name"
                :rules="[requiredValidator]"
                placeholder="John"
              />
            </VCol>

            <!-- 👉 Billing Email -->
            <VCol
              cols="12"
              md="6"
            >
              <AppTextField
                v-model="email"
                label="Email"
                type="email"
                :rules="[requiredValidator, emailValidator]"
                placeholder="johndoe@email.com"
              />
            </VCol>

            <!-- 👉 Billing Phone -->
            <VCol
              cols="12"
              md="6"
            >
              <AppTextField
                v-model="phone"
                label="Phone"
                :rules="[requiredValidator]"
                placeholder="590000000"
              />
            </VCol>

            <!-- 👉 Status -->
            <VCol
              cols="12"
              md="6"
            >
              <AppSelect
                v-model="is_active"
                label="is_active"
                placeholder="Active"
                :items="[
                  { title: $t('Active'), value: true },
                  { title: $t('Inactive'), value: false },
                ]"
              />
            </VCol>

            <!-- 👉 type -->
            <VCol
              cols="12"
              md="6"
            >
              <AppSelect
                v-model="type"
                label="type"
                placeholder="admin"
                :items="[
                  { title: $t('admin'), value: 'admin' },
                  { title: $t('user'), value: 'user' },
                ]"
              />
            </VCol>

            <!-- 👉 Submit and Cancel -->
            <VCol
              cols="12"
              class="d-flex flex-wrap justify-center gap-4"
            >
              <VBtn type="submit">
                Submit
              </VBtn>

              <VBtn
                color="secondary"
                variant="tonal"
                @click="onFormReset"
              >
                Cancel
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </VDialog>
</template>
