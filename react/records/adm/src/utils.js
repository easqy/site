import { SelectControl } from '@wordpress/components';

export const EasqySelect = (props) => {

	const { allLabel, options, value, setState, label } = props;
	const o = [];
	if (allLabel)
		o.push({ label: allLabel, value: -1 });

	options.forEach((c, i) => o.push({ label: c, value: i }));

	return <SelectControl
		disabled={props.disabled}
		label={label || ''}
		value={value}
		onChange={(c) => { setState(parseInt(c)) }}
		options={o}
	/>
}
