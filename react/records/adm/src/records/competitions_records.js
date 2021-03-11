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
			currentRecordId: -1
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
					<div>
						<CompetitionTable
							categories={categories}
							epreuves={epreuves}
							genres={genres}
							records={records}
							onCurrentChanged={(rId) => this.onCurrentChanged(rId)}
							currentRecordId={currentRecordId}
						/>
					</div>
					<div id={'easqy-current-record'}>
						{(currentRecordId >= 0) &&
							/*
							<CurrentRecord
								key={this.state.currentRecordId}
								createMode={false}
								categories={categories}
								epreuves={epreuves}
								genres={genres}
								athletes={athletes}
								records={records}
								ra={ra}
								onAthletesChanged={() => { }}
								onCancel={() => { }}
								record={record}
							/>
							*/
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
