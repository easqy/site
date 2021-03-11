import { Component } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';
import { EasqySelect } from '../utils';

export default class CompetitionTable extends Component {

	constructor(props) {
		super(props);
		const {
			categories,
			epreuves,
			genres,
			records,
		} = this.props;

		this.state = {
			loading: true,
			categories,
			epreuves,
			genres,
			records,

			filterCategorie: -1,
			filterEpreuve: -1,
			filterGenre: -1,
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
		this.props.onCurrentChanged(r);
		/*
				this.props.onCurrentChanged(r);
				if (r.i === this.state.currentRecordId)
					this.setState({ currentRecordId: -1 });
				else
					this.setState({ currentRecordId: r.i });
		*/
	}

	render() {
		const { records, categories, epreuves, genres } = this.state

		const rows = records.filter(r => {

			let result = true;
			if (result && (this.state.filterCategorie !== -1))
				result = (r.c === this.state.filterCategorie);

			if (result && (this.state.filterEpreuve !== -1))
				result = (r.e === this.state.filterEpreuve);

			if (result && (this.state.filterGenre !== -1))
				result = (r.g === this.state.filterGenre);

			if (result && (this.state.filterIndoor !== -1))
				result = (r.in === this.state.filterIndoor);

			return result;
		});

		if (this.props.currentRecordId >= 0) {
			const currentRowVisible = rows.find(r => r.i === this.props.currentRecordId);
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
								value={this.state.filterCategories} />
						</th>
						<th>Épreuve<br />
							<EasqySelect
								allLabel={'Toutes'}
								options={epreuves}
								setState={this.updateEpreuveFilter.bind(this)}
								value={this.state.filterEpreuve} />
						</th>
						<th>Genre<br />
							<EasqySelect
								allLabel={'Tous'}
								options={genres}
								setState={this.updateGenreFilter.bind(this)}
								value={this.state.filterGenre} />
						</th>
						<th>Indoor<br />
							<SelectControl
								value={this.state.filterIndoor}
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
									className={'records' + (r.i === this.props.currentRecordId ? ' selected' : '')}
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

