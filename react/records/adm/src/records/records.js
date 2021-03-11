
import { Component } from '@wordpress/element';
import Competitions from './competitions';
import $ from "jquery";
import EditRecord from './edit_record';

const ViewType = {
	loading: 0,
	error: 1,
	competitions: 2,
	add: 3,
	modify: 4
};
Object.freeze(ViewType);

export default class Records extends Component {

	constructor(props) {
		super(props);
		this.state = {
			viewType: ViewType.loading,
			currentRecord: {},
		};

		this._k = 1
		this.props = { ...this.props, records: [], athletes: [], categories: [], genres: [], epreuves: [], ra: [] }
	}

	componentDidMount() {
		this.loadRecords();
	}

	loadRecords() {

		this._k = this._k + 1;
		this.setState({ viewType: ViewType.loading });
		this._k = this._k + 1;

		const me = this;
		$.ajax({
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			url: ajaxurl,
			method: "GET",
			data: {
				action: "easqy_records",
				security: easqy_records_adm.security
			},
			success: function (data) {
				const { records, athletes, ra, categories, genres, epreuves } = data.data;
				//console.log(data.data);

				me.props.categories = categories;
				me.props.epreuves = epreuves;
				me.props.genres = genres;
				me.props.athletes = athletes;
				me.props.records = records;
				me.props.ra = ra;

				me.setState({
					viewType: ViewType.competitions
				});
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ viewType: ViewType.error });
			},
		});
	}

	doDelRecord(rId) {
		const me = this;
		$.ajax({
			url: easqy_records_adm.ajaxurl,
			method: "POST",
			data: {
				action: "easqy_record_del",
				security: easqy_records_adm.security,
				recordId: rId
			},
			success: function (data) {
				if (!data.success)
					me.setState({ viewType: ViewType.error });
				else {
					console.log('record id ', rId, 'deleted', data);
					me.loadRecords();
				}
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ viewType: ViewType.error });
			},
		});
	}

	doSaveRecord(record) {

		const rec = { ...record }
		rec.date = { y: record.date.getFullYear(), m: record.date.getMonth(), d: record.date.getDate() }

		const me = this;
		$.ajax({
			url: easqy_records_adm.ajaxurl,
			method: "POST",
			data: {
				action: "easqy_record_save",
				security: easqy_records_adm.security,
				record: rec
			},
			success: function (data) {
				if (!data.success)
					me.setState({ viewType: ViewType.error });
				else {
					console.log('record id ', rec, 'saved', data);
					me.loadRecords();
				}
			},
			error: (data) => {
				console.log("error", data);
				me.setState({ viewType: ViewType.error });
			},
		});
	}

	render() {

		const {
			categories,
			epreuves,
			genres,
			records,
			ra,
			athletes
		} = this.props;

		const {
			viewType,
			currentRecord
		} = this.state;

		const header = () => (
			<div>
				<h1 class="wp-heading-inline">Records - Gestion des Records</h1>&nbsp;
				{(viewType === ViewType.competitions) && (
					<a
						href="#"
						className={"page-title-action"}
						onClick={(e) => { this.setState({ viewType: ViewType.add }) }}
					>
						Ajouter
					</a>
				)}
			</div>
		);

		const competitions = () => (
			<Competitions
				key={this._k}
				categories={categories}
				epreuves={epreuves}
				genres={genres}
				athletes={athletes}
				records={records}
				ra={ra}
				currentRecordId={this.state.currentRecord.i || -1}
				doDelRecord={(rId) => { this.doDelRecord(rId) }}
				doModifyRecord={(record) => { this.setState({ currentRecord: record, viewType: ViewType.modify }) }}
			/>);

		const addRecord = () => (
			<EditRecord
				createMode={true}
				categories={categories}
				epreuves={epreuves}
				genres={genres}
				athletes={athletes}
				records={records}
				ra={ra}
				onAthletesChanged={() => { }}
				onCancel={() => { this.setState({ viewType: ViewType.competitions }) }}
				onSave={(r) => { this.doSaveRecord(r) }}
			/>);

		const modifyRecord = () => (
			<EditRecord
				createMode={false}
				record={currentRecord}
				categories={categories}
				epreuves={epreuves}
				genres={genres}
				athletes={athletes}
				records={records}
				ra={ra}
				onAthletesChanged={() => { }}
				onCancel={() => { this.setState({ viewType: ViewType.competitions }) }}
				onSave={(r) => { this.doSaveRecord(r) }}
			/>);


		return (
			<div id="easqy-records-adm" className="wrap">
				{header()}
				{(viewType === ViewType.competitions) && (competitions())}
				{(viewType === ViewType.add) && (addRecord())}
				{(viewType === ViewType.modify) && (modifyRecord())}
			</div>
		)
	}
}
