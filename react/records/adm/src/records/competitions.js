import { Component } from '@wordpress/element';
import CompetitionTable from './competition_table';
import CurrentRecord from './currentrecord';

export default class CompetitionRecords extends Component {

	constructor(props) {
		super(props);
		const {
			categories,
			epreuves,
			genres,
			records,
			athletes, ra
		} = this.props;

		this.state = {
			currentRecordId: this.props.currentRecordId || -1
		};
	}

	onCurrentChanged(rId) {
		this.setState({ currentRecordId: rId });
	}

	render() {

		const {
			categories,
			epreuves,
			genres,
			records,
			ra,
			athletes,
		} = this.props;

		const { currentRecordId } = this.state;

		const record = records.find(r => r.i === currentRecordId);
		return (
			<>
				<div id={'easqy-records-header'}>
					{records.length} records, {athletes.length} athletes.
				</div>
				<div className={'wrapper'}>
					<div id={'easqy-current-competitions'}>
						<CompetitionTable
							categories={categories}
							epreuves={epreuves}
							genres={genres}
							records={records}
							onCurrentChanged={(rId) => this.onCurrentChanged(rId)}
							currentRecordId={currentRecordId}
						/>
						<hr />
					</div>
					<div id={'easqy-current-record'}>
						{(currentRecordId >= 0) &&
							<CurrentRecord
								record={record}
								athletes={athletes}
								categories={categories}
								ra={ra}
								doDel={() => { this.props.doDelRecord && this.props.doDelRecord(record.i) }}
								doModify={() => {
									this.props.doModifyRecord && this.props.doModifyRecord(record)
								}}
							/>
						}
					</div>
				</div>
			</>
		)
	}

}
