import { Component } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';
import { EasqySelect } from '../utils';

export default class CompetitionTable extends Component {

	constructor(props) {
		super(props);
		const record = this.props.records.find(r => r.i === this.props.currentRecordId);
		this.state = {
			loading: true,

			filterCategorie: record ? record.c : -1,
			filterEpreuve: record ? record.e : -1,
			filterGenre: record ? record.g : -1,
			filterIndoor: -1
		};
	}

	updateCategoryFilter(c) {
		this.setState({ filterCategorie: c })
	}

	updateEpreuveFilter(c) {
		this.setState({ filterEpreuve: c })
	}

	updateGenreFilter(c) {
		this.setState({ filterGenre: c })
	}

	setCurrentRecordId(r) {
		this.props.onCurrentChanged && this.props.onCurrentChanged(r);
	}

	render() {
		const { records, categories, epreuves, genres, currentRecordId } = this.props
		const { filterCategorie, filterEpreuve, filterGenre, filterIndoor } = this.state

		const rows = records.filter(r => {

			let result = true;
			if (result && (filterCategorie !== -1))
				result = (r.c === filterCategorie);

			if (result && (filterEpreuve !== -1))
				result = (r.e === filterEpreuve);

			if (result && (filterGenre !== -1))
				result = (r.g === filterGenre);

			if (result && (filterIndoor !== -1))
				result = (r.in === filterIndoor);

			return result;
		});

		if (currentRecordId >= 0) {
			const currentRowVisible = rows.find(r => r.i === currentRecordId);
			if (currentRowVisible === undefined)
				this.setCurrentRecordId(-1);
		}

		return (
			<table className={'records-table'}>
				<thead>
					<tr>
						<th colSpan="4">Compétition</th>
					</tr>
					<tr>
						<th>Catégorie<br />
							<EasqySelect
								allLabel={'Toutes'}
								options={categories}
								setState={(c) => { this.updateCategoryFilter(c) }}
								value={filterCategorie} />
						</th>
						<th>Épreuve<br />
							<EasqySelect
								allLabel={'Toutes'}
								options={epreuves}
								setState={this.updateEpreuveFilter.bind(this)}
								value={filterEpreuve} />
						</th>
						<th>Genre<br />
							<EasqySelect
								allLabel={'Tous'}
								options={genres}
								setState={this.updateGenreFilter.bind(this)}
								value={filterGenre} />
						</th>
						<th>Indoor<br />
							<SelectControl
								value={filterIndoor}
								onChange={(c) => { this.setState({ filterIndoor: parseInt(c) }) }}
								options={[{ label: 'Tous', value: -1 }, { label: 'Oui', value: 1 }, { label: 'Non', value: 0 }]}
							/>
						</th>
					</tr>
				</thead>
				<tbody>
					{
						rows.map(r => {
							return (
								<tr
									id={'record-' + r.i}
									key={r.i}
									onClick={() => this.setCurrentRecordId(r.i)}
									className={'records' + (r.i === currentRecordId ? ' selected' : '')}
								>
									<td>{categories[r.c]}</td>
									<td>{epreuves[r.e]}</td>
									<td>{genres[r.g]}</td>
									<td>{r.in ? 'oui' : 'non'}</td>
								</tr>
							);
						})
					}
				</tbody>
			</table>
		)
	}
}

