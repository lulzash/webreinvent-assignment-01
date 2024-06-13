<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-6">
                <div id="task-container" class="p-3">
                    <section id="task-header" class="mt-3">
                        <h3 class="text-center">Task List</h3>
                    </section>
                    <section id="task-errors">
                        <div v-if="createTaskForm.errors.length > 0"
                            class="alert alert-warning alert-dismissible fade show" role="alert">
                            <span v-for="(error, index) in createTaskForm.errors" :key="index">{{ error }}</span>
                        </div>
                        <div v-if="createTaskForm.isCreated" class="alert alert-success alert-dismissible fade show"
                            role="alert">
                            <span>Task item created successfully</span>
                        </div>
                        <div v-if="editTaskForm.isUpdated" class="alert alert-success alert-dismissible fade show"
                            role="alert">
                            <span>Task item updated successfully</span>
                        </div>
                    </section>
                    <section id="add-task-form" class="my-3">
                        <form>
                            <div class="d-flex justify-content-between align-items-center">
                                <input v-model="createTaskForm.name" v-on:keyup.enter="addTask" minlength="5"
                                    maxlength="50" placeholder="Enter task name" type="text" class="form-control mr-3">
                                <button v-if="createTaskForm.isSubmitting" class="btn btn-primary" type="button"
                                    disabled>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                </button>
                                <button v-else @click.prevent="addTask" class="m-3 btn btn-primary btn-sm">Add</button>
                            </div>
                        </form>
                    </section>

                    <section id="view-all-tasks" class="my-3">
                        <button @click.prevent="getAllTasks(this.showCompletedTasks)" class="btn btn-primary btn-sm"> {{
                            this.showCompletedTasks ? 'View Pending Tasks' : 'View All Tasks' }}
                        </button>
                    </section>

                    <section id="task-actions"></section>
                    <section id="task-list">
                        <ul class="list-group">
                            <div v-if="tasks.isLoading" class="text-center">
                                <div class="spinner-border" role="status">
                                </div>
                            </div>
                            <li v-if="!tasks.isLoading && tasks.data.length > 0" v-for="task in tasks.data"
                                :key="task.id" class="list-group-item">
                                <input type="checkbox" v-model="task.is_completed" @change="markAsCompleted(task)">
                                <div class="d-flex justify-content-between align-items-center">
                                    {{ task.name }}
                                    <span class="d-flex justify-content-between align-items-center">
                                        <a class="text-success mr-2 m-2" href="#"
                                            @click.prevent="showEditTaskForm(task)">
                                            <i class="fa fa-edit"> </i> Edit
                                        </a>
                                        <a class="text-danger" href="#" @click.prevent="destroy(task)">
                                            <i class="fa fa-trash-o"></i> Delete
                                        </a>
                                    </span>
                                </div>
                                <div class="d-flex w-100 justify-content-between">
                                    <small class="text-muted">Status</small>
                                    <small class="text-muted">{{ task.is_completed ? 'Completed' : 'Pending' }}</small>
                                </div>
                            </li>
                            <li v-if="!tasks.isLoading && tasks.data.length === 0"
                                class="list-group-item list-group-item-action list-group-item-warning">
                                No task items found.
                            </li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="editTaskItemModal" tabindex="-1" role="dialog"
            aria-labelledby="editTaskItemModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTaskItemModalLabel">Update Task</h5>
                    </div>
                    <div class="modal-body">
                        <section id="edit-task-errors" class="mb-3">
                            <div v-if="editTaskForm.errors.length > 0"
                                class="alert alert-warning alert-dismissible fade show" role="alert">
                                <span v-for="(error, index) in editTaskForm.errors" :key="index">{{ error }}</span>
                            </div>
                        </section>
                        <section id="edit-task">
                            <form>
                                <div class="d-flex justify-content-between align-items-center">
                                    <input v-model="editTaskForm.name" v-on:keyup.enter="updateTask" minlength="5"
                                        maxlength="50" placeholder="Enter task name and press enter" type="text"
                                        class="form-control mr-3">
                                    <button v-if="editTaskForm.isSubmitting" class="btn btn-primary" type="button"
                                        disabled>
                                        <span class="spinner-grow spinner-grow-sm" role="status"
                                            aria-hidden="true"></span>
                                    </button>
                                    <button v-else @click.prevent="updateTask"
                                        class="btn btn-success m-2 btn-sm">Update</button>
                                </div>
                            </form>
                        </section>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            onclick="$('#editTaskItemModal').modal('hide')">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import _ from 'lodash';
import bootstrap from 'bootstrap';

export default {
    name: 'TaskList',
    data() {
        return {
            tasks: {
                isLoading: false,
                data: []
            },
            createTaskForm: {
                isSubmitting: false,
                isCreated: false,
                name: undefined,
                errors: []
            },
            editTaskForm: {
                isSubmitting: false,
                isUpdated: false,
                id: undefined,
                name: undefined,
                errors: []
            },
            showCompletedTasks: false,
            error: undefined
        }
    },
    mounted() {
        this.getTaskList(this.showCompletedTasks);
    },
    methods: {
        showEditTaskForm(task) {
            this.editTaskForm.name = task.name;
            this.editTaskForm.id = task.id;
            $('#editTaskItemModal').modal('show');
        },

        getAllTasks() {
            this.showCompletedTasks = !this.showCompletedTasks;
            this.getTaskList(this.showCompletedTasks);
        },

        getTaskList(completed = false) {
            this.tasks.isLoading = true;
            axios.get('/api/tasks', {
                params: {
                    completed: completed
                }
            })
                .then((response) => {
                    this.tasks.data = response.data;
                })
                .catch((error) => {
                    console.log(error.message);
                    this.error = 'Unable to load tasks.';
                })
                .finally(() => {
                    this.tasks.isLoading = false;
                })
        },

        addTask() {
            this.createTaskForm.isSubmitting = true;

            axios.post('/api/tasks', this.createTaskForm)
                .then((response) => {
                    this.createTaskForm.errors = [];
                    this.createTaskForm.title = undefined;
                    this.getTaskList(this.showCompletedTasks);
                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        this.createTaskForm.errors = _.flatten(_.toArray(error.response.data.errors));
                        // show error at form input


                    } else {
                        console.log(error.message);
                        this.error = 'Unable to add task.';
                    }
                })
                .finally(() => {
                    this.createTaskForm.isSubmitting = false;
                })
        },

        updateTask() {

            axios.put(`/api/tasks/${this.editTaskForm.id}`, this.editTaskForm)
                .then((response) => {
                    $('#editTaskItemModal').modal('hide');
                    this.editTaskForm.name = undefined;
                    this.editTaskForm.id = undefined;
                    this.getTaskList(this.showCompletedTasks);
                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        this.editTaskForm.errors = _.flatten(_.toArray(error.response.data.errors));
                    } else {
                        console.log(error.message);
                        this.error = 'Unable to update task.';
                    }
                });
        },

        destroy(taskItem) {
            if (!confirm('Are you sure you want to delete this task?')) {
                return;
            }

            axios.delete(`/api/tasks/${taskItem.id}`)
                .then((response) => {
                    this.showCompletedTasks = false;
                    this.getTaskList(this.showCompletedTasks);
                })
                .catch((error) => {
                    console.log(error.message);
                    this.error = 'Unable to delete task.';
                })
        },

        markAsCompleted(taskItem) {
            let complete = this.showCompletedTasks ? 'incomplete' : 'complete';
            axios.patch(`/api/tasks/${taskItem.id}/${complete}`)
                .then((response) => {
                    this.getTaskList(this.showCompletedTasks);
                })
                .catch((error) => {
                    console.log(error.message);
                    this.error = 'Unable to mark task as completed.';
                })
        }
    }
}
</script>