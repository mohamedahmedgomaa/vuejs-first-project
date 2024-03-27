<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'userData',
])

const isFormValid = ref(false)
const refForm = ref()
const name = ref('')
const phone = ref('')
const email = ref('')
const password = ref('')
const type = ref()
const isActive = ref()


const errors = ref({
  email: undefined,
  password: undefined,
  name: undefined,
  type: undefined,
  phone: undefined,
  isActive: undefined,
})

// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}

const onSubmit = () => {
  refForm.value?.validate().then(async ({ valid }) => {
    if (valid) {
      const res = await $api('/users/crud', {
        method: 'POST',
        body: {
          name: name.value,
          type: type.value,
          email: email.value,
          password: password.value,
          phone: phone.value,
          "is_active": isActive.value,
        },
        onResponseError({ response }) {
          errors.value = response._data.errors
        },
      })

      console.log(errors.value)
      await nextTick(() => {
        emit('userData', {
          id: 0,
          name: name.value,
          type: type.value,
          email: email.value,
          password: password.value,
          phone: phone.value,
          isActive: isActive.value,
        })

        emit('update:isDrawerOpen', false)
        refForm.value?.reset()
        refForm.value?.resetValidation()
      })
    }
  })
}

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}
</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <AppDrawerHeaderSection
      title="Add New User"
      @cancel="closeNavigationDrawer"
    />

    <VDivider />

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- 👉 Form -->
          <VForm
            ref="refForm"
            v-model="isFormValid"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Full name -->
              <VCol cols="12">
                <AppTextField
                  v-model="name"
                  :rules="[requiredValidator]"
                  label="Name"
                  placeholder="Name"
                  :error-messages="errors.name"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">
                <AppTextField
                  v-model="email"
                  :rules="[requiredValidator, emailValidator]"
                  label="Email"
                  placeholder="johndoe@email.com"
                  :error-messages="errors.email"
                />
              </VCol>

              <!-- 👉 Password -->
              <VCol cols="12">
                <AppTextField
                  v-model="password"
                  type="password"
                  :rules="[requiredValidator]"
                  label="Password"
                  placeholder="password"
                  :error-messages="errors.password"
                />
              </VCol>

              <!-- 👉 Phone -->
              <VCol cols="12">
                <AppTextField
                  v-model="phone"
                  :rules="[requiredValidator]"
                  label="Phone"
                  placeholder="Phone"
                  :error-messages="errors.phone"
                />
              </VCol>

              <!-- 👉 Role -->
              <VCol cols="12">
                <AppSelect
                  v-model="type"
                  label="Select Type"
                  placeholder="Select Role"
                  :rules="[requiredValidator]"
                  :items="[{ title: 'Admin', value: 'admin' }, { title: 'User', value: 'user' }]"
                  :error-messages="errors.type"
                />
              </VCol>

              <!-- 👉 Status -->
              <VCol cols="12">
                <AppSelect
                  v-model="isActive"
                  label="Select Status"
                  placeholder="Select Status"
                  :items="[{ title: 'Active', value: true }, { title: 'Inactive', value: false }]"
                />
              </VCol>

              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  Submit
                </VBtn>
                <VBtn
                  type="reset"
                  variant="tonal"
                  color="error"
                  @click="closeNavigationDrawer"
                >
                  Cancel
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>
