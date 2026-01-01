<template>
  <div class="bg-white shadow rounded p-6">
    <div class="mb-3 text-sm text-gray-600">Question {{ index + 1 }} of {{ total }}</div>
    <div class="mb-4 text-lg font-medium" v-html="question.text"></div>

    <div v-if="isObjective">
        <label v-for="opt in question.options" :key="opt.id" class="flex items-center p-2 border rounded mb-2 cursor-pointer hover:bg-gray-50">
            <input type="radio" :name="'q-' + question.id" :value="opt.id" v-model="localAnswer" class="mr-3"/>
            <span v-html="opt.text"></span>
        </label>
    </div>

    <!-- Multi-select -->
    <div v-else-if="isMulti">
      <label v-for="opt in question.options" :key="opt.id" class="flex items-center p-2 border rounded mb-2 cursor-pointer hover:bg-gray-50">
        <input
          type="checkbox"
          :value="opt.id"
          v-model="localAnswerArray"
          class="mr-3"
        />
        <span v-html="opt.text"></span>
      </label>
    </div>

    <!-- Theory -->
    <div v-else-if="isTheory">
      <textarea v-model="localAnswerText" rows="7" class="w-full border rounded p-3" placeholder="Type your answer here..."></textarea>
    </div>

    <div class="mt-4 text-sm text-gray-500">
      <span v-if="question.required">* This question is required.</span>
      <span v-else>Optional</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'QuestionCard',
  props: {
    question: { type: Object, required: true },
    answer: { required: false }, // current answer from parent
    index: { type: Number, required: true },
    total: { type: Number, required: true }
  },
  data: function () {
    return {
      localAnswer: null,
      localAnswerArray: [],
      localAnswerText: ''
    };
  },
  computed: {
    isObjective: function () {
      return this.question.type === 'objective';
    },
    isMulti: function () {
      var t = this.question.type;
      return t === 'multi' || t === 'multi-select';
    },
    isTheory: function () {
      var t = this.question.type;
      return t === 'theory' || t === 'essay';
    }
  },
  watch: {
    // initialize when props.answer or question change
    answer: {
      immediate: true,
      handler: function (v) {
        if (this.isObjective) {
          this.localAnswer = v ?? null;
        } else if (this.isMulti) {
          this.localAnswerArray = Array.isArray(v) ? v.slice() : [];
        } else if (this.isTheory) {
          this.localAnswerText = v ?? '';
        }
      }
    },
    // propagate changes up
    localAnswer: function (val) {
      this.$emit('update:answer', val);
    },
    localAnswerArray: function (val) {
      this.$emit('update:answer', val);
    },
    localAnswerText: function (val) {
      this.$emit('update:answer', val);
    }
  }
};
</script>

<style scoped>
/* tiny style adjustments if needed */
</style>
