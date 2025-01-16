<script setup lang="ts">
const styleOptions = {
	default: 'bg-ui-500 text-ui-500-contrast hover:bg-ui-800 hover:text-ui-800-contrast',
	defaultOutline:
		'outline-2 outline-ui-500 focus:outline-ui-900 hover:bg-ui-800 hover:text-ui-800-contrast hover:outline-ui-800 dark:hover:outline-ui-700 focus:outline-ui-800 dark:focus:outline-ui-700',
	navigation:
		'text-ui-900 bg-ui-100 hover:bg-ui-500 outline-ui-200 hover:text-ui-500-contrast dark:hover:bg-ui-800 dark:hover:text-ui-800-contrast',
	primary:
		'bg-primary-500 outline-0 hover:outline-2 hover:outline-primary-900 outline-primary-500 text-primary-500-contrast hover:bg-primary-800 hover:text-primary-800-contrast',
	primaryOutline:
		'outline-2 outline-primary-500 focus:outline-primary-900 hover:bg-primary-800 hover:text-primary-800-contrast hover:outline-primary-800',
	secondary:
		'bg-secondary-500 outline-0 hover:outline-2 hover:outline-secondary-900 dark:bg-secondary-800 text-secondary-500-contrast dark:text-secondary-800-contrast hover:bg-secondary-800 hover:text-secondary-800-contrast dark:hover:bg-secondary-700 dark:hover:text-secondary-700-contrast',
	secondaryOutline:
		'outline-2 outline-secondary-500 focus:outline-secondary-900 dark:outline-secondary-700 dark:hover:outline-secondary-900 hover:bg-secondary-800 hover:text-secondary-800-contrast dark:hover:bg-secondary-800 dark:hover:text-secondary-800-contrast hover:outline-secondary-800',
};
const sizeOptions = {
	default: 'p-2 px-4',
	sm: 'p-1 px-2',
	inherit: 'p-inherit px-inherit',
};

withDefaults(
	defineProps<{
		style?: keyof typeof styleOptions;
		size?: keyof typeof sizeOptions;
		active?: boolean;
		anchor?: string;
		anchorTarget?: '_blank' | '_self';
		styleOverrides?: string;
	}>(),
	{
		style: 'default',
		size: 'default',
		active: false,
		anchor: '',
		anchorTarget: '_blank',
		styleOverrides: '',
	},
);
const emit = defineEmits<{
	(e: 'clicked'): void;
}>();
</script>
<template>
	<a
		v-if="anchor"
		class="group cursor-pointer rounded transition-all select-none focus:outline-2 focus:outline-offset-2"
		:class="
			styleOptions[style] +
			' ' +
			sizeOptions[size] +
			' ' +
			(active ? 'underline' : '') +
			' ' +
			styleOverrides
		"
		:href="anchor"
		:target="anchorTarget"
	>
		<slot></slot>
	</a>
	<button
		v-if="!anchor"
		class="group cursor-pointer rounded transition-all select-none focus:outline-2 focus:outline-offset-2"
		:class="
			styleOptions[style] +
			' ' +
			sizeOptions[size] +
			' ' +
			(active ? 'underline' : '') +
			' ' +
			styleOverrides
		"
		@click="emit('clicked')"
	>
		<slot></slot>
	</button>
</template>
